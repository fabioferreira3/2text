<?php

namespace App\Packages\ChatGPT;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGPT
{
    protected string $baseUri;
    protected string $model;
    protected array $defaultMessages;

    public function __construct()
    {
        $this->baseUri = 'https://api.openai.com/v1/chat/completions';
        $this->model = 'gpt-3.5-turbo';
        $this->defaultMessages = [
            [
                'role' => 'system',
                'content' => ''
            ]
        ];
    }

    public function request(array $messages)
    {
        $response = Http::connectTimeout(45)->acceptJson()->withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY')
        ])->retry(20, 20000)->post($this->baseUri, [
            'model' => $this->model,
            'messages' => [
                ...$this->defaultMessages,
                ...$messages
            ]
        ]);

        if ($response->successful()) {
            return $response;
        }

        if ($response->failed()) {
            Log::error($response);
            throw new Exception($response);
        }
    }

    public function countTokens($string)
    {
        return Artisan::call('count:token', ['string' => addslashes($string)]);
    }
}
