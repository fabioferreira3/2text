<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Helpers\MediaHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\Traits\JobEndings;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessSocialMediaPostsCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected $document;
    protected $meta;

    public function __construct($document, array $meta = [])
    {
        $this->document = $document;
        $this->meta = $meta;
    }

    public function handle()
    {
        try {
            $this->document->refresh();

            foreach ($this->meta['platforms'] as $platform => $value) {
                $platformName = Str::of($platform)->lower();
                $textProcessId = Str::uuid();
                $post = Document::create([
                    'type' => DocumentType::SOCIAL_MEDIA_POST->value,
                    'account_id' => $this->document->account_id,
                    'meta' => [
                        'platform' => $platformName
                    ]
                ]);
                $this->document->children()->save($post);

                DocumentRepository::createTask(
                    $post->id,
                    DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST,
                    [
                        'process_id' => $textProcessId,
                        'meta' => [
                            'platform' => $platformName,
                            'query_embedding' => true
                        ],
                        'order' => 1
                    ]
                );

                if ($post->getMeta('generate_img')) {
                    $imageSize = MediaHelper::getSocialMediaImageSize($platformName);
                    $processId = Str::uuid();

                    DocumentRepository::createTask(
                        $post->id,
                        DocumentTaskEnum::GENERATE_IMAGE,
                        [
                            'order' => 1,
                            'process_id' => $processId,
                            'meta' => [
                                'process_id' => $processId,
                                'prompt' => $post->getMeta('img_prompt'),
                                'height' => $imageSize['height'],
                                'width' => $imageSize['width'],
                                'add_content_block' => true,
                                'style_preset' => $post->getMeta('img_style'),
                                'steps' => 21
                            ]
                        ]
                    );
                    DocumentRepository::createTask(
                        $post->id,
                        DocumentTaskEnum::REGISTER_FINISHED_PROCESS,
                        [
                            'order' => 2,
                            'process_id' => $processId,
                            'meta' => [
                                'silently' => true
                            ]
                        ]
                    );
                }

                DocumentRepository::createTask(
                    $post->id,
                    DocumentTaskEnum::REGISTER_FINISHED_PROCESS,
                    [
                        'order' => 2,
                        'process_id' => $textProcessId,
                        'meta' => [
                            'silently' => true
                        ]
                    ]
                );
                DispatchDocumentTasks::dispatch($post);
            }
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to create social media posts: ' . $e->getMessage());
        }
    }
}
