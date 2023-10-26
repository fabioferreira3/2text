<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DataType;
use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Helpers\MediaHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessSocialMediaPosts
{
    use Dispatchable, SerializesModels;

    protected $document;
    protected $platforms;
    protected $repo;
    protected string $processId;

    public function __construct($document, array $platforms)
    {
        $this->document = $document;
        $this->platforms = collect($platforms)->filter();
        $this->processId = Str::uuid();
        $this->repo = new DocumentRepository();
        $this->repo->setDocument($document);
    }

    public function handle()
    {
        // DocumentRepository::createTask(
        //     $this->document->id,
        //     DocumentTaskEnum::SUMMARIZE_DOC,
        //     [
        //         'order' => 1,
        //         'process_id' => $this->processId
        //     ]
        // );

        $this->document->refresh();

        if ($this->document->meta['source'] === 'website_url') {
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::EMBED_SOURCE,
                [
                    'process_id' => $this->processId,
                    'meta' => [
                        'data_type' => DataType::WEB_PAGE,
                        'source' => $this->document->getMeta('source_url')
                    ],
                    'order' => 2
                ]
            );
        } elseif ($this->document->meta['source'] === 'youtube') {
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::DOWNLOAD_AUDIO,
                [
                    'process_id' => $this->processId,
                    'meta' => [
                        'source_url' => $this->document->meta['source_url']
                    ],
                    'order' => 2
                ]
            );
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::PROCESS_AUDIO,
                [
                    'process_id' => $this->processId,
                    'meta' => [],
                    'order' => 3
                ]
            );
        }

        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::CREATE_TITLE,
            [
                'process_id' => $this->processId,
                'meta' => [],
                'order' => 99
            ]
        );

        DispatchDocumentTasks::dispatch($this->document);

        $this->platforms->each(function ($value, $platform) {
            $platformName = Str::of($platform)->lower();
            $textProcessId = Str::uuid();
            $post = Document::create([
                'type' => DocumentType::SOCIAL_MEDIA_POST->value,
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
                        'platform' => $platformName
                    ],
                    'order' => 1
                ]
            );

            if ($post->getMeta('generate_img')) {
                $imageSize = MediaHelper::socialMediaImageSize($platformName);
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
        });
    }
}
