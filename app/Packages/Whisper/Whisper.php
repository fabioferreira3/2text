<?php

namespace App\Packages\Whisper;

use OpenAI\Laravel\Facades\OpenAI;

class Whisper
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function request()
    {
        $response = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($this->file, 'r'),
            'response_format' => 'verbose_json',
        ]);

        return [
            'text' => $response->text
        ];
    }
}
