<?php

namespace App\Factories;

use App\Interfaces\WhisperFactoryInterface;
use App\Packages\Whisper\Whisper;

class WhisperFactory implements WhisperFactoryInterface
{
    public function make($file): Whisper
    {
        return new Whisper($file);
    }
}
