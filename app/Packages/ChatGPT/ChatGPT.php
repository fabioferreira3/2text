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
        //$this->model = 'gpt-4';
        $this->defaultMessages = [
            [
                'role' => 'system',
                'content' => 'Strictly follow the instructions. Never mention you are ChatGPT or an AI assistant.'
            ]
        ];
    }

    public function request(array $messages)
    {
        $response = Http::connectTimeout(10)->acceptJson()->withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY')
        ])->retry(5, 100)->post($this->baseUri, [
            'model' => $this->model,
            'messages' => [
                ...$this->defaultMessages,
                ...$messages
            ]
        ]);

        Log::debug($response);

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
