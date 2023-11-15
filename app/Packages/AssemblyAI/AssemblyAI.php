<?php

namespace App\Packages\AssemblyAI;

use App\Packages\AssemblyAI\Exceptions\CheckStatusRequestException;
use App\Packages\AssemblyAI\Exceptions\TranscribeRequestException;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssemblyAI
{
    protected $client;
    protected $defaultBody;

    public function __construct()
    {
        $this->client = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->withToken(config('assemblyai.token'), 'Bearer')
            ->baseUrl('https://api.assemblyai.com/v2')
            ->timeout(90);
    }

    public function transcribe($fileUrl)
    {
        try {
            $response = $this->client
                ->post('/transcript', [
                    'audio_url' => $fileUrl,
                    'webhook_url' => config('assemblyai.webhook_url'),
                    'webhook_auth_header_name' => 'Authorization',
                    'webhook_auth_header_value' => 'Bearer ' . config('assemblyai.token'),
                    'speaker_labels' => true
                ]);

            if ($response->failed()) {
                return $response->throw();
            }

            if ($response->successful()) {
                return json_decode($response->body(), true);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new TranscribeRequestException($e->getMessage());
        }
    }

    public function checkStatus($transcriptionId)
    {
        try {
            $response = $this->client->get('/transcript/' . $transcriptionId);
            if ($response->successful()) {
                return json_decode($response->body(), true);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new CheckStatusRequestException($e->getMessage());
        }
    }
}
