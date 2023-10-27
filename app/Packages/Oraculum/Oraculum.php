<?php

namespace App\Packages\Oraculum;

use App\Enums\DataType;
use App\Models\User;
use App\Packages\Oraculum\Exceptions\AddSourceException;
use App\Packages\Oraculum\Exceptions\ChatRequestException;
use App\Packages\Oraculum\Exceptions\CreateBotException;
use App\Packages\Oraculum\Exceptions\QueryRequestException;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class Oraculum
{
    protected $client;
    protected $defaultBody;
    protected $user;
    protected $taskId;

    public function __construct(User $user, string $taskId)
    {
        $this->user = $user;
        $token = JWTAuth::fromUser($this->user);
        $this->taskId = $taskId;
        $this->defaultBody = [
            'app_id' => $this->user->id,
            'token' => $token
        ];

        $this->client = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept-Encoding' => 'gzip, deflate, br'
        ])->baseUrl(config('oraculum.url'));
    }

    public function createBot()
    {
        try {
            if ($this->user->bot_enabled) {
                return;
            }

            $response = $this->client
                ->post('/new_user_bot', $this->defaultBody);

            if ($response->failed()) {
                $response->throw();
            }

            if ($response->successful()) {
                $this->user->update(['bot_enabled' => true]);
            }
        } catch (Exception $e) {
            throw new CreateBotException($e->getMessage());
        }
    }

    public function add(DataType $dataType, string $source)
    {
        try {
            if (!$this->user->bot_enabled) {
                $this->createBot();
            }

            $response = $this->client
                ->post('/add', array_merge($this->defaultBody, [
                    'collection_name' => $this->taskId,
                    'data_type' => $dataType->value,
                    'url_or_text' => $source
                ]));

            if ($response->failed()) {
                $response->throw();
            }

            if ($response->successful()) {
                return $response->json('data');
            }
        } catch (Exception $e) {
            throw new AddSourceException($e->getMessage());
        }
    }

    public function query($message)
    {
        try {
            if (!$this->user->chat_enabled) {
                $this->createBot();
            }

            $requestTokens = $this->countTokens($message);

            $response = $this->client
                ->post('/query', array_merge($this->defaultBody, [
                    'collection_name' => $this->taskId,
                    'message' => $message
                ]));

            if ($response->failed()) {
                return $response->throw();
            }

            if ($response->successful()) {
                $responseTokens = $this->countTokens($response->json('data'));
                return [
                    'data' => $response->json('data'),
                    'token_usage' => [
                        'prompt' => $requestTokens,
                        'completion' => $responseTokens,
                        'total' => $requestTokens + $responseTokens
                    ]
                ];
            }
        } catch (Exception $e) {
            throw new QueryRequestException($e->getMessage());
        }
    }

    public function chat($message)
    {
        try {
            if (!$this->user->chat_enabled) {
                $this->createBot();
            }

            $requestTokens = $this->countTokens($message);

            $response = $this->client
                ->post('/chat', array_merge($this->defaultBody, [
                    'collection_name' => $this->taskId,
                    'message' => $message
                ]));

            if ($response->failed()) {
                return $response->throw();
            }

            if ($response->successful()) {
                $responseTokens = $this->countTokens($response->json('data'));
                return [
                    'data' => $response->json('data'),
                    'token_usage' => [
                        'prompt' => $requestTokens,
                        'completion' => $responseTokens,
                        'total' => $requestTokens + $responseTokens
                    ]
                ];
            }
        } catch (Exception $e) {
            throw new ChatRequestException($e->getMessage());
        }
    }

    public function countTokens($string)
    {
        Artisan::call('count:token', ['string' => addslashes($string)]);
        return (int) Artisan::output();
    }
}
