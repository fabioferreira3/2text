<?php

namespace App\Adapters;

use App\Enums\AIModel;
use App\Interfaces\ImageGeneratorInterface;
use App\Packages\OpenAI\DallE;

/**
 * @codeCoverageIgnore
 */
class DallEHandler implements ImageGeneratorInterface
{
    public function textToImage(array $params)
    {
        $client = new DallE($params['model'] ?? AIModel::DALL_E_3->value);
        return $client->request([
            'n' => $params['samples'] ?? 1,
            'size' => $params['width'] . 'x' . $params['height'],
            'prompt' => $params['prompt'],
            'quality' => $params['quality'] ?? 'standard',
        ]);
    }

    public function imageToImage(array $params)
    {
    }
}
