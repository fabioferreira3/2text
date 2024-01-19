<?php

namespace App\Packages\OpenAI;

use App\Enums\AIModel;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use OpenAI\Factory as OpenAI;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChatGPT
{
    protected string $model;
    protected array $defaultMessages;
    protected bool $shouldStream;

    public function __construct($model = null, $shouldStream = false)
    {
        $this->model = $model ?? AIModel::GPT_4_TURBO->value;
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
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error("HTTP request failed: " . $e->getMessage());
            throw new HttpException($e->getCode(), $e->getMessage());
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
