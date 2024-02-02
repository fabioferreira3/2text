<?php

namespace App\Repositories;

use Illuminate\Support\Str;

class UnitRepository
{
    public function estimateCost($taskType, $meta)
    {
        $handler = 'handle' . Str::studly($taskType);
        return $this->$handler($meta);
    }

    public function handleWordsGeneration(array $params)
    {
        return number_format($params['word_count'] / 480, 2);
    }

    public function handleImageGeneration(array $params)
    {
        return $params['img_count'];
    }

    public function handleAudioTranscription(array $params)
    {
        return $params['duration'] * 0.1;
    }

    public function handleAudioGeneration(array $params)
    {
        return $params['word_count'] / 70;
    }
}
