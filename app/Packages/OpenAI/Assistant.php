<?php

namespace App\Packages\OpenAI;

use App\Domain\Thread\Enum\MessageRole;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenAI\Factory as OpenAI;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @codeCoverageIgnore
 */
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
            ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
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
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::error("HTTP request failed: " . $e->getMessage());
            throw new HttpException($e->getCode(), $e->getMessage());
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

    // Threads

    public function createThread(array $params = [])
    {
        return $this->getClient()->threads()->create($params);
    }

    public function createAndRunThread(array $params)
    {
        return $this->getClient()->threads()->createAndRun($params);
    }

    // Messages

    public function createMessage(string $externalThreadId, MessageRole $role, string $content)
    {
        return $this->getClient()->threads()->messages()->create($externalThreadId, [
            'role' => $role->value,
            'content' => $content,
        ]);
    }

    public function retrieveThreadMessages(string $externalThreadId, string $runId = null)
    {
        $response = $this->getClient()->threads()->messages()->list($externalThreadId, [
            'run_id' => $runId
        ]);

        $messages = [];

        foreach ($response->data as $message) {
            $messages[] = [
                'id' => $message->id,
                'created_at' => $message->createdAt,
                'role' => $message->role,
                'content' => $message->content[0]->text->value
            ];
        }

        return $messages;
    }

    // Runs

    public function createRun(string $externalThreadId, string $assistantId)
    {
        return $this->getClient()->threads()->runs()->create(
            threadId: $externalThreadId,
            parameters: [
                'assistant_id' => $assistantId,
            ],
        );
    }

    public function retrieveRun(string $externalThreadId, string $runId)
    {
        return $this->getClient()->threads()->runs()->retrieve(
            threadId: $externalThreadId,
            runId: $runId
        );
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
}
