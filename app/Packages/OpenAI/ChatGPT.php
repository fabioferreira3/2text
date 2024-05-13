<?php

namespace App\Packages\OpenAI;

use App\Enums\AIModel;
use App\Helpers\SupportHelper;
use Exception;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use OpenAI\Factory as OpenAI;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @codeCoverageIgnore
 */
class ChatGPT
{
    public string $model;
    public array $defaultMessages;
    public bool $shouldStream;

    public function __construct($model = null, $shouldStream = false)
    {
        $this->model = $model ?? AIModel::GPT_LATEST->value;
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
        if (SupportHelper::isTestModeEnabled()) {
            return $this->mockResponse($messages);
        }

        try {
            $factory = new OpenAI();
            $client = $factory
                ->withApiKey(env('OPENAI_API_KEY'))
                ->withHttpClient($client = new \GuzzleHttp\Client([
                    'timeout' => 300.0
                ]))
                ->make();
            $response = $client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ...$this->defaultMessages,
                    ...$messages
                ]
            ]);

            foreach ($response->choices as $result) {
                if ($result->message->role === 'assistant') {
                    if ($result->finishReason !== 'stop') {
                        Log::error("finish reason: " . $result->finishReason);
                        Log::error($result->message->content);
                    }

                    return [
                        'content' => $result->message->content,
                        'token_usage' => [
                            'model' => $response->model,
                            'prompt' => $response->usage->promptTokens,
                            'completion' => $response->usage->completionTokens,
                            'total' => $response->usage->totalTokens
                        ]
                    ];
                }
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error("HTTP request failed: " . $e->getMessage());
            throw new HttpException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function countTokens($string)
    {
        Artisan::call('count:token', ['string' => addslashes($string)]);
        return (int) Artisan::output();
    }

    private function mockResponse(array $messages)
    {
        $faker = Faker::create();
        $sleepCounter = $faker->numberBetween(2, 6);
        $wordsCount = $faker->numberBetween(10, 250);
        $response = $faker->words($wordsCount, true);
        $promptTokens = $this->countTokens($messages[0]['content']);
        $completionTokens = $this->countTokens($response);

        if (isset($messages[0]['task'])) {
            switch ($messages[0]['task']) {
                case 'generate_thoughts':
                    $response = json_encode([
                        $faker->sentence(),
                        $faker->sentence(),
                        $faker->sentence()
                    ]);
                    break;
                case 'create_title':
                    $response = $faker->sentence();
                    break;
                case 'create_outline':
                    $response = '1. Main Topic A. Subtopic 1 B. Subtopic 2 C. Subtopic 3';
                    break;
                case 'expand_outline':
                    $response = "<h2>{$faker->sentence()}</h2><p>{$faker->text(200)}</p><h2>{$faker->sentence()}</h2><p>{$faker->text(200)}</p><h2>{$faker->sentence()}</h2><p>{$faker->text(200)}</p>";
                    break;
                case 'expand_text_section':
                    $response = "<p>{$faker->text(200)}</p><p>{$faker->text(200)}</p><p>{$faker->text(200)}</p>";
                    break;
                default:
                    break;
            }
        }

        sleep($sleepCounter);
        return [
            'content' => $response,
            'token_usage' => [
                'model' => 'gpt-4',
                'prompt' => $promptTokens,
                'completion' => $completionTokens,
                'total' => $promptTokens + $completionTokens
            ]
        ];
    }
}
