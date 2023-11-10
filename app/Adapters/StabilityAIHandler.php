<?php

namespace App\Adapters;

use App\Interfaces\ImageGeneratorInterface;
use Illuminate\Support\Facades\Storage;
use Talendor\StabilityAI\Enums\StylePreset;
use Talendor\StabilityAI\StabilityAIClient;

class StabilityAIHandler implements ImageGeneratorInterface
{
    public function textToImage(array $params)
    {
    }

    public function imageToImage(array $params)
    {
        $client = app(StabilityAIClient::class);
        $params['init_image'] = Storage::disk('s3')->get($params['file_name']);

        return $client->imageToImage([
            'init_image' => $params['init_image'],
            'style_preset' => $params['style_preset'] ?? StylePreset::DIGITAL_ART->value,
            'weight' => $params['weight'] ?? 1,
            'samples' => $params['samples'] ?? 4,
            'steps' => $params['steps'] ?? 21,
            'prompt' => $params['prompt']
        ]);
    }
}
