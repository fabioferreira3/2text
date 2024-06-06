<?php

namespace App\Packages\Anthropic;

use Anthropic\Laravel\Facades\Anthropic;
use App\Enums\AIModel;
use App\Helpers\SupportHelper;
use Exception;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @codeCoverageIgnore
 */
class Claude
{
    public string $model;
    public array $defaultMessages;
    public bool $shouldStream;

    public function __construct($model = null, $shouldStream = false)
    {
        $this->model = $model ?? AIModel::CLAUDE3_SONNET->value;
        $this->shouldStream = $shouldStream;
        $this->defaultMessages = [];
    }

    public function request(array $messages)
    {
        if (SupportHelper::isTestModeEnabled()) {
            return $this->mockResponse($messages);
        }

        try {
            $response = Anthropic::messages()->create([
                'model' => $this->model,
                'max_tokens' => 1024,
                'system' => 'Strictly follow the instructions. Do not mention you are Claude or an AI assistant.',
                'messages' => $messages,
            ]);

            if ($response->role === 'assistant') {
                if ($response->stop_reason !== 'end_turn') {
                    Log::error("finish reason: " . $response->stop_reason);
                    Log::error($response->content);
                }

                return [
                    'content' => $response->content[0]->text,
                    'token_usage' => [
                        'model' => $this->model,
                        'prompt' => $response->usage->inputTokens,
                        'completion' => $response->usage->outputTokens,
                        'total' => $response->usage->inputTokens + $response->usage->outputTokens
                    ]
                ];
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
