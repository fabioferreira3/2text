<?php

namespace App\Repositories;

use App\Packages\ChatGPT\ChatGPT;
use App\Enums\LanguageModels;
use App\Helpers\PromptHelper;
use App\Models\Document;
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

    public static function paraphraseText(Document $document, $text, $tone = null)
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper('en');
        $chatGpt = new ChatGPT();
        $response = $chatGpt->request([
            [
                'role' => 'user',
                'content' =>   $promptHelper->paraphrase($text, $tone)
            ]
        ]);
        $repo->addHistory(
            [
                'field' => 'partial_content',
                'content' => $response['content']
            ],
            $response['token_usage']
        );

        return $response['content'];
    }
}
