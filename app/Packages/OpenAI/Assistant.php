<?php

namespace App\Packages\OpenAI;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use OpenAI\Factory as OpenAI;

class Assistant
{
    protected string $model;

    public function __construct()
    {
    }

    public function getClient()
    {
        $factory = new OpenAI();
        $client = $factory
            ->withApiKey(env('OPENAI_API_KEY'))
            ->withHttpHeader('OpenAI-Beta', 'assistants=v1')
            ->withHttpClient($client = new \GuzzleHttp\Client([
                'timeout' => 300.0
            ]))
            ->make();

        return $client;
    }

    public function create()
    {
        try {
            $client = $this->getClient();

            $response = $client->assistants()->create([
                'model' => 'gpt-4-1106-preview',
                'name' => 'Oraculum',
                'tools' => [
                    ['type' => 'retrieval']
                ],
                'instructions' => 'Your name is Oraculum, the main android behind Experior\'s work of generating
                    content, so act as a friendly android. Do not mention ever that you are ChatGPT.',
            ]);

            return $response->toArray();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function embed()
    {
        $client = $this->getClient();
        $file = Storage::disk('local')->get('/public/emergency.pdf');
        $response = $client->files()->upload([
            'purpose' => 'assistants',
            'file' => $file,
        ]);

        return $response->toArray();
    }

    public function addFileToAssistant()
    {
        $client = $this->getClient();

        $response = $client->assistants()->files()->create(
            'asst_aa23CPXawJ80kwZMVStIGswR',
            ['file_id' => 'file-yAdMpA9RTtqz1KAwaMs9jn6U']
        );

        return $response->toArray();
    }

    public function retrieveFile()
    {
        $client = $this->getClient();
        return $client->files()->retrieve('file-yAdMpA9RTtqz1KAwaMs9jn6U');
    }

    public function createThread()
    {
        $client = $this->getClient();
        return $client->threads()->create([]);
    }

    public function query($message)
    {
        $threadId = 'thread_MhC6BoQqtPxH04Be0OXZt90q';
        $client = $this->getClient();
        $client->threads()->messages()->create($threadId, [
            'role' => 'user',
            'content' => $message,
        ]);

        return $client->threads()->runs()->create(
            threadId: $threadId,
            parameters: [
                'assistant_id' => 'asst_aa23CPXawJ80kwZMVStIGswR',
            ],
        );
    }

    public function listMessages()
    {
        // $runId = "run_ur0DfGhmRN1qAmaNloLDrVhB";
        // $threadId = 'thread_MhC6BoQqtPxH04Be0OXZt90q';
        // $client = $this->getClient();
        // return $client->threads()->runs()->retrieve(
        //     threadId: $threadId,
        //     runId: $runId,
        // );

        $client = $this->getClient();
        $response = $client->threads()->messages()->list('thread_MhC6BoQqtPxH04Be0OXZt90q', [
            'limit' => 10,
        ]);

        $messages = [];

        foreach ($response->data as $message) {
            $messages[] = [
                'id' => $message->id,
                'created_at' => $message->createdAt,
                'content' => $message->content[0]->text->value
            ];
        }

        return $messages;
    }
}
