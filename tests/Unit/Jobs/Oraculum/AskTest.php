<?php

use App\Enums\DocumentTaskEnum;
use App\Events\ChatMessageReceived;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\Oraculum\Ask;
use App\Jobs\RegisterAppUsage;
use App\Jobs\RegisterUnitsConsumption;
use App\Models\ChatThread;
use App\Models\ChatThreadIteration;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Packages\Oraculum\Oraculum;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->be($this->authUser);
    $this->chatThreadIteration = ChatThreadIteration::factory()->create([
        'origin' => 'user',
        'chat_thread_id' => ChatThread::factory()->create([
            'user_id' => $this->authUser->id,
            'document_id' => Document::factory()->create()
        ])
    ]);
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->chatThreadIteration->thread->document_id
    ]);
});

describe('Oraculum Ask job', function () {
    it('handles the questions and registers the response', function () {
        Event::fake(ChatMessageReceived::class);

        expect($this->documentTask->status)->toBe('ready');

        $job = new Ask($this->chatThreadIteration, [
            'collection_name' => $this->chatThreadIteration->thread->document_id,
            'task_id' => $this->documentTask->id,
        ]);
        $job->handle();
        $latestIteration = ChatThreadIteration::fromSys()->first();

        expect($this->documentTask->fresh()->status)->toBe('finished');

        Bus::assertDispatched(RegisterUnitsConsumption::class, function ($job) use ($latestIteration) {
            return $job->account->id === $latestIteration->thread->document->account_id
                && $job->type === 'words_generation'
                && $job->meta['word_count'] === 3;
        });

        Bus::assertDispatched(RegisterAppUsage::class, function ($job) {
            return $job->account->id === $this->chatThreadIteration->thread->document->account_id
                && $job->params['meta']['name'] === DocumentTaskEnum::ASK_ORACULUM->value;
        });

        Event::assertDispatched(ChatMessageReceived::class, function ($event) use ($latestIteration) {
            return $event->iteration->id === $latestIteration->id;
        });
    });

    it('handles exceptions', function () {
        Event::fake(ChatMessageReceived::class);

        $oraculum = Mockery::mock(Oraculum::class);
        $oraculum->shouldReceive('request')->andThrow(new Exception('Error'));
        $oraculumFactory = Mockery::mock(OraculumFactoryInterface::class);
        $oraculumFactory->shouldReceive('make')->andReturn($oraculum);
        $this->app->instance(OraculumFactoryInterface::class, $oraculumFactory);

        $job = new Ask($this->chatThreadIteration, [
            'collection_name' => $this->chatThreadIteration->thread->document_id,
            'task_id' => $this->documentTask->id,
        ]);
        $job->handle();

        expect($this->documentTask->fresh()->status)->toBe('failed');

        Bus::assertNotDispatched(RegisterUnitsConsumption::class);
        Bus::assertNotDispatched(RegisterAppUsage::class);
        Event::assertNotDispatched(ChatMessageReceived::class);
    });
})->group('oraculum');
