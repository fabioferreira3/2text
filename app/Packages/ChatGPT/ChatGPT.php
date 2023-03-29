<?php

namespace App\Packages\ChatGPT;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class ChatGPT
{
    protected string $model;
    protected array $defaultMessages;
    protected bool $shouldStream;

    public function __construct($shouldStream = false)
    {
        //$this->model = 'gpt-3.5-turbo';
        $this->model = 'gpt-4';
        //$this->model = 'gpt-4-0314';
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
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ...$this->defaultMessages,
                ...$messages
            ]
        ]);

        foreach ($response->choices as $result) {
            if ($result->finishReason !== 'stop') {
                Log::debug("finish reason: " . $result->finishReason);
                Log::debug($result->message->content);
            }
            if ($result->message->role === 'assistant' && $result->finishReason === 'stop') {
                return $result->message->content;
            }
        }
    }

    // public function request(array $messages)
    // {
    //     $response = Http::connectTimeout(60)->timeout(60)->acceptJson()->withHeaders([
    //         'Authorization' => 'Bearer ' . env('OPENAI_API_KEY')
    //     ])->retry(2, 15000)->post($this->baseUri, [
    //         'model' => $this->model,
    //         'stream' => $this->shouldStream,
    //         'messages' => [
    //             ...$this->defaultMessages,
    //             ...$messages
    //         ]
    //     ]);

    //     if ($this->shouldStream && $response) {
    //         $stream = "";
    //         $responseStrings = explode("\n", $response->body());
    //         foreach ($responseStrings as $string) {
    //             if ($string !== '') {
    //                 $stringResponse = Str::replaceFirst('data: ', '', $string);
    //                 $arrayResponse = json_decode($stringResponse, true);
    //                 if (isset($arrayResponse['choices'][0]['delta']['content']) && !$arrayResponse['choices'][0]['finish_reason']) {
    //                     $stream .= $arrayResponse['choices'][0]['delta']['content'];
    //                     Log::debug($stream);
    //                 };
    //             }
    //         }
    //     }

    //     if ($response->successful() && !$this->shouldStream) {
    //         return $response;
    //     }

    //     if ($response->failed()) {
    //         Log::error($response);
    //     }

    //     if ($response->serverError()) {
    //         Log::error($response);
    //     }

    //     if ($response->clientError()) {
    //         Log::error($response);
    //     }

    //     if ($this->shouldStream && $response) {
    //         return $stream;
    //     }
    // }

    public function countTokens($string)
    {
        return Artisan::call('count:token', ['string' => addslashes($string)]);
    }
}
