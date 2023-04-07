<?php

namespace App\Repositories;

use App\Enums\ChatGptModel;
use App\Helpers\PromptHelper;
use App\Helpers\TextRequestHelper;
use App\Models\TextRequest;
use App\Packages\ChatGPT\ChatGPT;
use Illuminate\Support\Str;

class TextRequestRepository
{
    protected PromptHelper $promptHelper;

    public function __construct()
    {
        $this->promptHelper = new PromptHelper();
    }

    public function create(array $params): TextRequest
    {
        return TextRequest::create($params);
    }

    public function saveField(TextRequest $textRequest, array $payload, array $tokenUsage = [])
    {
        $textRequest->update([$payload['field'] => $payload['content']]);
        if (count($tokenUsage)) {
            $this->logModelChange($textRequest, $payload, $tokenUsage);
        }
    }

    public function logModelChange(TextRequest $textRequest, array $payload, array $tokenUsage)
    {
        $content = is_array($payload['content']) ? json_encode($payload['content']) : $payload['content'];
        $textRequest->logs()->create([
            'type' => $payload['field'], 'content' => $content,
            'prompt_token_usage' => $tokenUsage['prompt'],
            'completion_token_usage' => $tokenUsage['completion'],
            'total_token_usage' => $tokenUsage['total'],
            'model' => $tokenUsage['model']
        ]);
    }

    public function resetRawStructure(TextRequest $textRequest)
    {
        return $textRequest->update(['raw_structure' => TextRequestHelper::parseOutlineToRawStructure($textRequest->outline)]);
    }

    public function createFirstPass(TextRequest $textRequest)
    {
        $chatGpt = new ChatGPT();
        $response = $chatGpt->request([
            [
                'role' => 'user',
                'content' =>  "Write a blog article, using a " . $textRequest->tone . " tone, using <p> tags to surround paragraphs, <h2> tags to surround main topics and <h3> tags to surround inner topics, based on the following outline: \n\n" . $textRequest->outline . "\n\n\nFurther instructions: Do not surround h2 and h3 tags with p tags, for example: \n\n Bad output:\n<p><h2>Topic</h2></p>\n\nBad output:\n<p><h2>Topic</h2><h3>Inner topic</h3></p>\n\n\nThe outline structure should be parsed to html tags like this:\n\nInput:\nA. Topic 1\n\nOutput:<h3>A. Topic 1</h3>\n\nInput:\nB. Topic 2\n\nOutput:<h3>B. Topic 2</h3>"
            ]
        ]);

        $textRequest->update(['raw_structure' => TextRequestHelper::parseHtmlTagsToRawStructure($response['content'])]);
        $this->saveField($textRequest, ['field' => 'final_text', 'content' => str_replace(["\r", "\n"], '', $textRequest->normalized_structure)], $response['token_usage']);
    }

    public function expandText(TextRequest $textRequest)
    {
        $this->promptHelper->setLanguage($textRequest->language);
        $chatGpt = new ChatGPT();
        $rawStructure = $textRequest->raw_structure;

        $prompt = $this->promptHelper->givenFollowingText($textRequest->normalized_structure);

        if (Str::wordCount($textRequest->normalized_structure) <= 1000) {
            $prompt .= $this->promptHelper->andGivenFollowingContext($textRequest->context);
        }

        foreach ($textRequest->raw_structure as $key => $section) {
            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' =>  $prompt . $this->promptHelper->expandOn($section['content'], $textRequest->tone)
                ]

            ]);
            $rawStructure[$key]['content'] = $response['content'];
            $this->saveField($textRequest, ['field' => 'raw_structure', 'content' => $rawStructure], $response['token_usage']);
            $textRequest->refresh();
        }

        $this->saveField($textRequest, ['field' => 'final_text', 'content' => str_replace(["\r", "\n"], '', $textRequest->normalized_structure)], []);
    }

    public function generateTitle(TextRequest $textRequest)
    {
        $this->promptHelper->setLanguage($textRequest->language);
        $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $this->promptHelper->writeTitle($textRequest->context, $textRequest->tone, $textRequest->keyword)
        ]]);
        $this->saveField($textRequest, ['field' => 'title', 'content' => $response['content']], $response['token_usage']);
    }

    public function generateSummary(TextRequest $textRequest)
    {
        $this->promptHelper->setLanguage($textRequest->language);
        $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);

        if ($textRequest->original_text_token_count < 2000) {
            return;
        }

        $sentences = collect(preg_split("/(?<=[.?!])\s+(?=([^\d\w]*[A-Z][^.?!]+))/", $textRequest->original_text, -1, PREG_SPLIT_NO_EMPTY));
        $paragraphs = collect([]);

        $sentences->chunk(12)->each(function ($chunk) use ($paragraphs) {
            $paragraphs->push($chunk);
        });

        $paragraphs = $paragraphs->map(function ($paragraph) {
            return $paragraph->join(' ');
        });

        $rewrittenParagraphs = collect([]);
        $messages = collect([]);

        // Paragraphs generation
        $paragraphs->each(function ($paragraph) use (&$messages, &$rewrittenParagraphs, &$chatGpt, &$textRequest) {
            $allContent = $messages->map(function ($message) {
                return $message['content'];
            })->join("");
            $tokenCount = $chatGpt->countTokens($allContent);
            $assistantContent = $messages->filter(function ($message) {
                return $message['role'] === 'assistant';
            })->map(function ($message) {
                return $message['content'];
            })->join("");

            if ($tokenCount > 2200) {
                $messages = collect([]);
                $rewrittenParagraphs = collect([]);
                $response = $chatGpt->request([[
                    'role' => 'user',
                    'content' => $this->promptHelper->summarize($assistantContent)
                ]]);
            } else {
                $messages->push([
                    'role' => 'user',
                    'content' => $this->promptHelper->rewriteWithSimilarWords($paragraph)
                ]);
                $response = $chatGpt->request($messages->toArray());
            }

            $messages->push([
                'role' => 'assistant',
                'content' => $response['content']
            ]);
            $rewrittenParagraphs->push($response['content']);
            $this->logModelChange($textRequest, ['field' => 'partial_summary', 'content' => $response['content']], $response['token_usage']);
        });
        $allRewrittenParagraphs = $rewrittenParagraphs->join(' ');
        $this->saveField($textRequest, ['field' => 'summary', 'content' => $allRewrittenParagraphs], []);
    }

    public function generateOutline(TextRequest $textRequest)
    {
        $this->promptHelper->setLanguage($textRequest->language);
        $chatGpt = new ChatGPT();
        $response = $chatGpt->request([
            [
                'role' => 'user',
                'content' =>   $this->promptHelper->writeOutline($textRequest->context, $textRequest->target_headers_count, $textRequest->tone)
            ]
        ]);

        $textRequest->update(['raw_structure' => TextRequestHelper::parseOutlineToRawStructure($response['content'])]);
        $this->saveField($textRequest, ['field' => 'outline', 'content' => $response['content']], $response['token_usage']);
    }

    public function generateMetaDescription(TextRequest $textRequest)
    {
        $this->promptHelper->setLanguage($textRequest->language);
        $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);
        $response = $chatGpt->request([[
            'role' => 'user',
            'content' => $this->promptHelper->writeMetaDescription($textRequest->context, $textRequest->tone, $textRequest->keyword)
        ]]);
        $this->saveField($textRequest, ['field' => 'meta_description', 'content' => $response['content']], $response['token_usage']);
    }
}
