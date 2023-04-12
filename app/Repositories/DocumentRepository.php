<?php

namespace App\Repositories;

use App\Enums\ChatGptModel;
use App\Enums\DocumentTaskEnum;
use App\Events\FailedTextRequest;
use App\Helpers\PromptHelper;
use App\Helpers\TextRequestHelper;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Models\TextRequest;
use App\Packages\ChatGPT\ChatGPT;
use Exception;
use Illuminate\Support\Str;

class DocumentRepository
{
    protected PromptHelper $promptHelper;

    public function __construct()
    {
        $this->promptHelper = new PromptHelper();
    }

    public function create(array $params): Document
    {
        return Document::create($params);
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
        try {
            $this->promptHelper->setLanguage($textRequest->language);
            $chatGpt = new ChatGPT();
            $response = $chatGpt->request([
                [
                    'role' => 'user',
                    'content' => $this->promptHelper->writeFirstPass($textRequest->tone, $textRequest->outline)
                ]
            ]);

            $textRequest->update(['raw_structure' => TextRequestHelper::parseHtmlTagsToRawStructure($response['content'])]);
            $this->saveField($textRequest, ['field' => 'draft_text', 'content' => str_replace(["\r", "\n"], '', $textRequest->normalized_structure)], $response['token_usage']);
        } catch (Exception $e) {
            event(new FailedTextRequest($textRequest, $e->getMessage()));
            throw new Exception('Failed to create first pass: ' . $e->getMessage());
        }
    }

    public function expandText(TextRequest $textRequest)
    {
        try {
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

            $this->saveField($textRequest, ['field' => 'draft_text', 'content' => str_replace(["\r", "\n"], '', $textRequest->normalized_structure)], []);
        } catch (Exception $e) {
            event(new FailedTextRequest($textRequest, $e->getMessage()));
            throw new Exception('Failed to expand text: ' . $e->getMessage());
        }
    }

    public function publishText(TextRequest $textRequest)
    {
        $this->saveField($textRequest, ['field' => 'final_text', 'content' => $textRequest->draft_text], []);
    }

    public function generateTitle(TextRequest $textRequest)
    {
        try {
            $this->promptHelper->setLanguage($textRequest->language);
            $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);
            $response = $chatGpt->request([[
                'role' => 'user',
                'content' => $this->promptHelper->writeTitle($textRequest->context, $textRequest->tone, $textRequest->keyword)
            ]]);
            $this->saveField($textRequest, ['field' => 'title', 'content' => $response['content']], $response['token_usage']);
        } catch (Exception $e) {
            event(new FailedTextRequest($textRequest, $e->getMessage()));
            throw new Exception('Failed to create title: ' . $e->getMessage());
        }
    }

    public function generateSummary(TextRequest $textRequest)
    {
        try {
            $this->promptHelper->setLanguage($textRequest->language);
            $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);

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

                if ($tokenCount > 2000) {
                    $messages = collect([]);
                    $rewrittenParagraphs = collect([]);
                    $response = $chatGpt->request([[
                        'role' => 'user',
                        'content' => $this->promptHelper->summarize($assistantContent)
                    ]]);
                } else {
                    $response = $chatGpt->request([[
                        'role' => 'user',
                        'content' => $this->promptHelper->simplify($paragraph)
                    ]]);
                }

                $rewrittenParagraphs->push($response['content']);
                $messages->push([
                    'role' => 'assistant',
                    'content' => $response['content']
                ]);
                $this->logModelChange($textRequest, ['field' => 'partial_summary', 'content' => $response['content']], $response['token_usage']);
            });
            $allRewrittenParagraphs = $rewrittenParagraphs->join(' ');
            $this->saveField($textRequest, ['field' => 'summary', 'content' => $allRewrittenParagraphs], []);
        } catch (Exception $e) {
            event(new FailedTextRequest($textRequest, $e->getMessage()));
            throw new Exception('Failed to generate summary: ' . $e->getMessage());
        }
    }

    public function generateOutline(TextRequest $textRequest)
    {
        try {
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
        } catch (Exception $e) {
            event(new FailedTextRequest($textRequest, $e->getMessage()));
            throw new Exception('Failed to generate outline: ' . $e->getMessage());
        }
    }

    public function generateMetaDescription(TextRequest $textRequest)
    {
        try {
            $this->promptHelper->setLanguage($textRequest->language);
            $chatGpt = new ChatGPT(ChatGptModel::GPT_3_TURBO->value);
            $response = $chatGpt->request([[
                'role' => 'user',
                'content' => $this->promptHelper->writeMetaDescription($textRequest->context, $textRequest->tone, $textRequest->keyword)
            ]]);
            $this->saveField($textRequest, ['field' => 'meta_description', 'content' => $response['content']], $response['token_usage']);
        } catch (Exception $e) {
            event(new FailedTextRequest($textRequest, $e->getMessage()));
            throw new Exception('Failed to generate meta description: ' . $e->getMessage());
        }
    }

    public function createTask(Document $document, DocumentTaskEnum $task, array $params)
    {
        DocumentTask::create([
            'name' => $task->value,
            'document_id' => $document->id,
            'process_id' => $params['process_id'],
            'job' => $task->getJob(),
            'status' => $params['status'] ?? 'pending',
            'meta' => $params['meta'] ?? [],
            'order' => $params['order'] ?? 1,
        ]);
    }
}
