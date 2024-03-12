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

    public $document;
    public $meta;
    public $textProcessId;
    public $imageProcessId;
    public $mediaHelper;

    public function __construct($document, array $meta = [])
    {
        $this->document = $document;
        $this->meta = $meta;
        $this->textProcessId = Str::uuid();
        $this->imageProcessId = Str::uuid();
        $this->mediaHelper = new MediaHelper();
    }

    public function handle()
    {
        try {
            $this->document->refresh();

            foreach ($this->meta['platforms'] as $platform => $value) {
                $processId = Str::uuid();
                $platformName = Str::of($platform)->lower();
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
                        'process_id' => $processId,
                        'process_group_id' => $this->textProcessId,
                        'meta' => [
                            'platform' => $platformName,
                            'query_embedding' => $this->meta['query_embedding'] ?? false,
                            'collection_name' => $this->document->id
                        ],
                        'order' => 1
                    ]
                );

                if ($post->getMeta('generate_img')) {
                    $imageSize = $this->mediaHelper->getSocialMediaImageSize($platformName);

                    DocumentRepository::createTask(
                        $post->id,
                        DocumentTaskEnum::GENERATE_IMAGE,
                        [
                            'order' => 1,
                            'process_id' => $this->imageProcessId,
                            'process_group_id' => $this->textProcessId,
                            'meta' => [
                                'process_id' => $this->imageProcessId,
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
                            'process_id' => $this->imageProcessId,
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
                        'process_id' => $processId,
                        'process_group_id' => $this->textProcessId,
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
