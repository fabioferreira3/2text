<?php

namespace App\Repositories;

use App\Packages\ChatGPT\ChatGPT;
use App\Enums\ChatGptModel;
use App\Helpers\PromptHelper;
use App\Models\Document;
use Illuminate\Support\Str;

class GenRepository
{
    public static function generateTitle(Document $document, array $meta = [])
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeTitle($document->normalized_structure, $meta['tone'], $meta['keyword'])
        ]]);
        $repo->updateMeta('title', Str::of(str_replace(["\r", "\n"], '', $response['content']))->trim()->trim('"'));
        $repo->addHistory(
            [
                'field' => 'title',
                'content' => $response['content']
            ],
            $response['token_usage']
        );
    }

    public static function generateMetaDescription(Document $document, array $meta = [])
    {
        $repo = new DocumentRepository($document);
        $promptHelper = new PromptHelper($document->language->value);
        $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $promptHelper->writeMetaDescription($document->normalized_structure, $meta['tone'], $meta['keyword'])
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
}
