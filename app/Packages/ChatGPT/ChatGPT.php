<?php

namespace App\Packages\ChatGPT;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Factory as OpenAIFactory;

class ChatGPT
{
    protected $client;
    protected string $model;
    protected array $defaultMessages;
    protected bool $shouldStream;

    public function __construct($model = 'gpt-4', $shouldStream = false)
    {
        $factory = new OpenAIFactory();
        $this->client = $factory
            ->withApiKey(env('OPENAI_API_KEY'))
            ->withHttpClient($this->client = new \GuzzleHttp\Client([
                'timeout' => 300.0
            ]))
            ->make();
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
        try {
            $response = $this->client->chat()->create([
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
        return Artisan::call('count:token', ['string' => addslashes($string)]);
    }
}
