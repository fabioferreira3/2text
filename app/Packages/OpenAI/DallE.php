<?php

namespace App\Packages\OpenAI;

use Exception;
use Illuminate\Support\Facades\Log;
use OpenAI\Factory as OpenAI;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DallE
{
    protected string $model;
    protected array $defaultParams;

    public function __construct($model = 'dall-e-3')
    {
        $this->model = $model;
        $this->defaultParams = [
            'size' => '1024x1024',
            'quality' => 'standard',
            'n' => 1
        ];
    }

    public function request(array $params)
    {
        try {
            $factory = new OpenAI();
            $client = $factory
                ->withApiKey(env('OPENAI_API_KEY'))
                ->withHttpClient($client = new \GuzzleHttp\Client([
                    'timeout' => 300.0
                ]))
                ->make();
            $response = $client->images()->create([
                'model' => $this->model,
                'response_format' => 'b64_json',
                'prompt' => $params['prompt'],
                'n' => $params['n'] ?? $this->defaultParams['n'],
                'size' => $params['size'] ?? $this->defaultParams['size'],
                'quality' => $params['quality'] ?? $this->defaultParams['quality'],
            ]);

            $data = [];

            foreach ($response->data as $data) {
                $imageData = base64_decode($data->b64_json);
                $fileName = uniqid() . '.png';
                return ['fileName' => $fileName, 'imageData' => $imageData];
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error("HTTP request failed: " . $e->getMessage());
            throw new HttpException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
