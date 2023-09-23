<?php

namespace App\Jobs\SocialMedia;

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Helpers\MediaHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Talendor\StabilityAI\Enums\StylePreset;

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
        DocumentRepository::createTask(
            $this->document->id,
            DocumentTaskEnum::SUMMARIZE_DOC,
            [
                'order' => 1,
                'process_id' => $this->processId
            ]
        );

        $this->document->refresh();

        if ($this->document->meta['source'] === 'website_url') {
            DocumentRepository::createTask(
                $this->document->id,
                DocumentTaskEnum::CRAWL_WEBSITE,
                [
                    'process_id' => $this->processId,
                    'meta' => [],
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
            $processId = Str::uuid();
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
                    'process_id' => $processId,
                    'meta' => [
                        'platform' => $platformName,
                        'generate_img' => $post->getMeta('generate_img')
                    ],
                    'order' => 1
                ]
            );

            if ($post->getMeta('generate_img')) {
                $imageSize = MediaHelper::socialMediaImageSize($platformName);

                DocumentRepository::createTask(
                    $post->id,
                    DocumentTaskEnum::GENERATE_IMAGE,
                    [
                        'order' => 2,
                        'process_id' => $processId,
                        'meta' => [
                            'prompt' => 'Anime illustration of a character bonding with a majestic dragon in a secluded mountain sanctuary, focusing on the size of the dragon and the affectionate interaction.',
                            'height' => $imageSize['height'],
                            'width' => $imageSize['width'],
                            'add_content_block' => true,
                            'style_preset' => StylePreset::CINEMATIC->value,
                            'steps' => 60
                        ]
                    ]
                );
            }

            DocumentRepository::createTask(
                $post->id,
                DocumentTaskEnum::REGISTER_FINISHED_PROCESS,
                [
                    'order' => 999,
                    'process_id' => $processId,
                    'meta' => [
                        'silently' => true
                    ]
                ]
            );
            DispatchDocumentTasks::dispatch($post);
        });
    }
}
