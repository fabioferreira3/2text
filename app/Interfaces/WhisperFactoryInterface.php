<?php

namespace App\Interfaces;

use App\Packages\Whisper\Whisper;

interface WhisperFactoryInterface
{
    public function make($file): Whisper;
}
