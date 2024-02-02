<?php

namespace App\Repositories;

use App\Adapters\ImageGeneratorHandler;
use App\Enums\DocumentTaskEnum;
use App\Enums\AIModel;
use App\Events\ProcessFinished;
use App\Helpers\PromptHelperFactory;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Talendor\StabilityAI\Enums\StabilityAIEngine;

class GenRepository
{

    public ChatGPTFactoryInterface $chatGptFactory;
    public OraculumFactoryInterface $oraculumFactory;
    public $response;

    public function __construct()
    {
        $this->chatGptFactory = app(ChatGPTFactoryInterface::class);
        $this->oraculumFactory = app(OraculumFactoryInterface::class);
        $this->response = null;
    }

    public function generateTitle(Document $document, $context)
    {
        $promptHelper = PromptHelperFactory::create($document->language->value);
        $chatGpt = $this->chatGptFactory->make(AIModel::GPT_3_TURBO1106->value);

        $this->response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeTitle($context, [
                'tone' => $document->getMeta('tone'),
                'keyword' => $document->getMeta('keyword')
            ])
        ]]);
        $generatedTitle = Str::of(str_replace(["\r", "\n"], '', $this->response['content']))->trim()->trim('"');
        $document->update(['title' => $generatedTitle]);

        return [
            'content' => $generatedTitle->value,
            'token_usage' => $this->response['token_usage']
        ];
    }

    public function generateEmbeddedTitle(Document $document, string $collectionName)
    {
        $user = User::findOrFail($document->getMeta('user_id'));
        $oraculum = $this->oraculumFactory->make($user, $collectionName);
        $promptHelper = PromptHelperFactory::create($document->language->value);
        $this->response = $oraculum->query($promptHelper->writeEmbeddedTitle([
            'tone' => $document->getMeta('tone'),
            'keyword' => $document->getMeta('keyword')
        ]));

        $document->update(['title' => $this->response['content']]);

        return $this->response;
    }

    public function generateMetaDescription(Document $document)
    {
        $promptHelper = PromptHelperFactory::create($document->language->value);
        $chatGpt = $this->chatGptFactory->make(AIModel::GPT_3_TURBO1106->value);
        return $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeMetaDescription(
                $document->getMeta('outline'),
                [
                    'tone' => $document->getMeta('tone'),
                    'keyword' => $document->getMeta('keyword')
                ]
            )
        ]]);
    }

    public function generateSummary(Document $document, array $params)
    {
        $promptHelper = PromptHelperFactory::create($document->language->value);
        $chatGpt = $this->chatGptFactory->make(AIModel::GPT_4_TURBO->value);
        $params['target_language'] = $document->getMeta('target_language') ?? null;

        return $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeSummary($params)
        ]]);
    }

    public function generateEmbeddedSummary(Document $document, array $params)
    {
        $user = User::findOrFail($document->getMeta('user_id'));
        $promptHelper = PromptHelperFactory::create($document->language->value);
        $oraculum = $this->oraculumFactory->make($user, $document->id);

        return $oraculum->query($promptHelper->writeEmbeddedSummary($params));
    }

    public static function generateImage(Document $document, array $params)
    {
        $handler = new ImageGeneratorHandler();
        $result = $handler->handle('textToImage', $params);

        if ($result) {
            $mediaFile = self::processImageResult($document, $result, $params, AIModel::DALL_E_3->value);
            if ($params['add_content_block'] ?? false) {
                $document->contentBlocks()->save(new DocumentContentBlock([
                    'type' => 'media_file_image',
                    'content' => $mediaFile->id,
                    'prompt' => $params['prompt'],
                    'order' => 1
                ]));
            }
        }

        event(new ProcessFinished($params['process_id']));
    }

    public static function generateImageVariants(Document $document, array $params)
    {
        $handler = new ImageGeneratorHandler();
        $results = $handler->handle('imageToImage', $params);
        // $client = app(StabilityAIClient::class);
        // $params['init_image'] = Storage::disk('s3')->get($params['file_name']);
        // $results = $client->imageToImage($params);
        if (count($results)) {
            foreach ($results as $result) {
                self::processImageResult($document, $result, $params, StabilityAIEngine::SD_XL_V_1->value);
            }
        }
    }

    public function generateSocialMediaPost(Document $document, string $platform)
    {
        Validator::make(
            ['platform' => Str::lower($platform)],
            ['platform' => 'in:linkedin,instagram,facebook,twitter']
        )->validate();

        $promptHelper = PromptHelperFactory::create($document->language->value);
        $chatGpt = $this->chatGptFactory->make();

        return $chatGpt->request([
            [
                'role' => 'user',
                'content' =>   $promptHelper->writeSocialMediaPost($document->getContext(), [
                    'keyword' => $document->getMeta('keyword'),
                    'platform' => $platform,
                    'tone' => $document->getMeta('tone'),
                    'style' => $document->getMeta('style'),
                    'target_word_count' => $document->getMeta('target_word_count'),
                    'more_instructions' => $document->getMeta('more_instructions')
                ])
            ]
        ]);
    }

    public function generateEmbeddedSocialMediaPost(Document $document, string $platform, string $collectionName)
    {
        Validator::make(
            ['platform' => Str::lower($platform)],
            ['platform' => 'in:linkedin,instagram,facebook,twitter']
        )->validate();

        $user = User::findOrFail($document->getMeta('user_id'));
        $oraculum = $this->oraculumFactory->make($user, $collectionName);
        $promptHelper = PromptHelperFactory::create($document->language->value);

        return $oraculum->query($promptHelper->writeEmbeddedSocialMediaPost([
            'query_embedded' => true,
            'keyword' => $document->getMeta('keyword'),
            'platform' => $platform,
            'tone' => $document->getMeta('tone'),
            'style' => $document->getMeta('style'),
            'target_word_count' => $document->getMeta('target_word_count'),
            'more_instructions' => $document->getMeta('more_instructions')
        ]));
    }

    public function rewriteTextBlock(DocumentContentBlock $contentBlock, array $params)
    {
        $promptHelper = PromptHelperFactory::create($contentBlock->document->language->value);
        $chatGpt = $this->chatGptFactory->make();
        $this->response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->generic($params['prompt'])
        ]]);
        $contentBlock->update(['content' => $this->response['content']]);

        return $this->response;
    }

    public function translateText($text, $targetLanguage)
    {
        $chatGpt = $this->chatGptFactory->make();
        $promptHelper = PromptHelperFactory::create('en');
        return $chatGpt->request([
            [
                'role' => 'user',
                'content' => $promptHelper->translate(
                    $text,
                    $targetLanguage
                )
            ]
        ]);
    }

    public static function paraphraseDocument(Document $document)
    {
        $document->refresh();

        foreach ($document->meta['sentences'] as $sentence) {
            DocumentRepository::createTask(
                $document->id,
                DocumentTaskEnum::PARAPHRASE_TEXT,
                [
                    'order' => 1,
                    'process_id' => Str::uuid(),
                    'meta' => [
                        'text' => $sentence['text'],
                        'tone' => $document->getMeta('tone'),
                        'sentence_order' => $sentence['sentence_order'],
                        'add_content_block' => $document->getMeta('add_content_block')
                    ]
                ]
            );
        }

        DispatchDocumentTasks::dispatch($document);
    }

    public static function textToAudio($document, array $params = [])
    {
        DocumentRepository::createTask(
            $document->id,
            DocumentTaskEnum::TEXT_TO_AUDIO,
            [
                'meta' => [
                    'input_text' => $params['input_text'],
                    'voice_id' => $params['voice_id'],
                ],
                'process_id' => $params['process_id']
            ]
        );

        DispatchDocumentTasks::dispatch($document);
    }

    private static function processImageResult($document, $result, $params, $model): MediaFile
    {
        $mediaFile = MediaRepository::storeImage($document->account, [
            'fileName' => $result['fileName'],
            'imageData' => $result['imageData'],
            'meta' => [
                'document_id' => $document->id,
                'process_id' => $params['process_id'] ?? null,
                'process_group_id' => $params['process_group_id'] ?? null,
                'style_preset' => $params['style_preset'] ?? null,
                'model' => $model,
                'steps' => $params['steps'] ?? 0,
                'prompt' => $params['prompt'] ?? null
            ]
        ]);

        RegisterUnitsConsumption::dispatch($document->account, 'image_generation', [
            'img_count' => 1,
            'document_id' => $document->id,
            'job' => DocumentTaskEnum::GENERATE_IMAGE->value
        ]);

        RegisterAppUsage::dispatch($document->account, [
            //'model' => StabilityAIEngine::SD_XL_V_1->value,
            'model' => AIModel::DALL_E_3->value,
            'size' => $params['size'] ?? '1024x1024',
            'meta' => [
                'document_id' => $document->id,
            ]
        ]);

        return $mediaFile;
    }
}
