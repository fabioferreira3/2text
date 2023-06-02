<?php

namespace App\Packages\ChatGPT;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use OpenAI\Factory as OpenAI;

class ChatGPT
{
    protected string $model;
    protected array $defaultMessages;
    protected bool $shouldStream;

    public function __construct($model = 'gpt-4', $shouldStream = false)
    {
        $this->model = $model;
        $this->shouldStream = $shouldStream;
        $this->defaultMessages = [
            [
                'role' => 'system',
                'content' => 'Strictly follow the instructions. Do not mention you are ChatGPT or an AI assistant.'
            ]
        ];
    }

    public function request(array $messages)
    {
        Log::debug($messages);
        return ['content' => "", 'token_usage' => []];
        try {
            $factory = new OpenAI();
            $client = $factory
                ->withApiKey(env('OPENAI_API_KEY'))
                ->withHttpClient($client = new \GuzzleHttp\Client([
                    'timeout' => 300.0
                ]))
                ->make();
            $response = $client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ...$this->defaultMessages,
                    ...$messages
                ]
            ]);

            foreach ($response->choices as $result) {
                if ($result->message->role === 'assistant') {
                    if ($result->finishReason !== 'stop') {
                        Log::error("finish reason: " . $result->finishReason);
                        Log::error($result->message->content);
                    }

                    return [
                        'content' => $result->message->content,
                        'token_usage' => [
                            'model' => $response->model,
                            'prompt' => $response->usage->promptTokens,
                            'completion' => $response->usage->completionTokens,
                            'total' => $response->usage->totalTokens
                        ]
                    ];
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function countTokens($string)
    {
        Artisan::call('count:token', ['string' => addslashes($string)]);
        return (int) Artisan::output();
    }
}
