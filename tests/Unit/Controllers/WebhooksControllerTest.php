<?php

use App\Enums\DocumentTaskEnum;
use App\Exceptions\WebhookException;
use App\Http\Controllers\WebhooksController;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Log::spy();
    Config::set('assemblyai.token', 'test_token');
});

describe('Webhooks Controller', function () {
    it('rejects the request with an invalid token', function () {
        $response = postJson(
            action([WebhooksController::class, 'assemblyAI']),
            [],
            ['Authorization' => 'Bearer invalid_token']
        );
        $response->assertForbidden();
        Log::shouldHaveReceived('error')->once();
    });

    it('rejects the request with an invalid IP address', function () {
        Request::macro('ip', function () {
            return 'invalid_ip';
        });

        $response = postJson(
            action([WebhooksController::class, 'assemblyAI']),
            [],
            ['Authorization' => 'Bearer test_token']
        );

        $response->assertForbidden();
        Log::shouldHaveReceived('error')->once();
    });

    it('processes a completed task', function () {
        $document = Document::factory()->create();
        $taskUuid = (string) Str::uuid();

        $data = [
            'status' => 'completed',
            'document_id' => $document->id,
            'task_id' => $taskUuid,
            'transcript_id' => 'transcript123',
        ];

        $request = Request::create(
            action([WebhooksController::class, 'assemblyAI']),
            'POST',
            $data,
            [],
            [],
            ['REMOTE_ADDR' => '44.238.19.20']
        );
        $request->headers->set('Authorization', 'Bearer test_token');
        $controller = new WebhooksController();
        $controller->assemblyAI($request);

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $document->id,
            'name' => DocumentTaskEnum::PROCESS_TRANSCRIPTION_RESULTS->value,
            'meta->transcript_id' => 'transcript123',
            'meta->pending_task_id' => $taskUuid,
        ]);
        Bus::assertDispatched(DispatchDocumentTasks::class);
    });

    it('handles exception', function () {
        $request = Mockery::mock(Request::class);
        $this->app->instance(Request::class, $request);
        $request->shouldReceive('bearerToken')
            ->andThrow(new Exception('test exception'));

        $controller = new WebhooksController();
        $controller->assemblyAI($request);
    })->throws(WebhookException::class);
})->group('controllers');
