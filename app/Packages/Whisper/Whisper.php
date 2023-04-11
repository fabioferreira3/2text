<?php

namespace App\Packages\Whisper;

use OpenAI\Factory as OpenAI;

class Whisper
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function request()
    {
        $factory = new OpenAI();
        $client = $factory
            ->withApiKey(env('OPENAI_API_KEY'))
            ->withHttpClient($client = new \GuzzleHttp\Client([
                'timeout' => 300.0
            ]))
            ->make();
        $response = $client->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($this->file, 'r'),
            'response_format' => 'verbose_json',
        ]);

        return [
            'text' => $response->text
        ];
    }
}
