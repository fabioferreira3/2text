<?php

namespace App\Repositories;

use App\Enums\DocumentTaskEnum;
use App\Packages\ChatGPT\ChatGPT;
use App\Enums\LanguageModels;
use App\Helpers\PromptHelper;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GenRepository
{
    public static function generateTitle(Document $document)
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT(LanguageModels::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeTitle($document->normalized_structure, [
                'tone' => $document['meta']['tone'] ?? null,
                'keyword' => $document['meta']['keyword']
            ])
        ]]);
        $document->update(['title' => Str::of(str_replace(["\r", "\n"], '', $response['content']))->trim()->trim('"')]);
        $repo->addHistory(
            [
                'field' => 'title',
                'content' => $response['content']
            ],
            $response['token_usage']
        );
    }

    public static function generateMetaDescription(Document $document)
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT(LanguageModels::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeMetaDescription(
                $document->normalized_structure,
                [
                    'tone' => $document['meta']['tone'] ?? null,
                    'keyword' => $document['meta']['keyword']
                ]
            )
        ]]);
        $repo->updateMeta('meta_description', Str::of(str_replace(["\r", "\n"], '', $response['content']))->trim()->trim('"'));
        $repo->addHistory(
            [
                'field' => 'meta_description',
                'content' => $response['content']
            ],
            $response['token_usage']
        );
    }

    public static function generateSocialMediaPost(Document $document, string $platform)
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT();
        $response = $chatGpt->request([
            [
                'role' => 'user',
                'content' =>   $promptHelper->writeSocialMediaPost($document->context, [
                    'keyword' => $document->meta['keyword'] ?? null,
                    'platform' => $platform,
                    'tone' => $document->meta['tone'] ?? null,
                    'style' => $document->meta['style'] ?? null,
                    'more_instructions' => $document->meta['more_instructions'] ?? null
                ])
            ]
        ]);
        $repo->updateMeta($platform, $response['content']);
        $repo->addHistory(
            [
                'field' => $platform,
                'content' => $response['content']
            ],
            $response['token_usage']
        );
    }

    public static function paraphraseDocument(Document $document)
    {
        $repo = new DocumentRepository($document);
        foreach ($document->meta['original_sentences'] as $sentence) {
            $repo->createTask(DocumentTaskEnum::PARAPHRASE_TEXT, [
                'order' => 1,
                'process_id' => Str::uuid(),
                'meta' => [
                    'text' => $sentence['text'],
                    'sentence_order' => $sentence['sentence_order'],
                    'user_id' => Auth::check() ? Auth::user()->id : null
                ]
            ]);
        }
        DispatchDocumentTasks::dispatch($document);
    }

    public static function paraphraseText(Document $document, array $params)
    {
        $repo = new DocumentRepository($document);
        $repo->createTask(DocumentTaskEnum::PARAPHRASE_TEXT, [
            'order' => 1,
            'process_id' => Str::uuid(),
            'meta' => [
                'text' => $params['text'],
                'sentence_order' => $params['sentence_order'],
                'tone' => $params['tone'] ?? null,
                'user_id' => Auth::check() ? Auth::user()->id : null
            ]
        ]);
        DispatchDocumentTasks::dispatch($document);
    }
}
