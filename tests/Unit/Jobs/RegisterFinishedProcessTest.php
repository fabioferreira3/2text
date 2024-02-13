<?php

use App\Events\ProcessFinished;
use App\Jobs\Contact\NotifyFinished;
use App\Jobs\RegisterFinishedProcess;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->be($this->authUser);
    $this->document = Document::factory()->create([
        'meta' => [
            'user_id' => $this->authUser->id
        ]
    ]);
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
        'process_id' => Str::uuid()
    ]);
});

describe('RegisterFinishedProcess job', function () {
    it('dispatches ProcessFinished event', function () {
        Event::fake();
        expect($this->documentTask->status)->toBe('ready');
        $job = new RegisterFinishedProcess($this->document, [
            'process_id' => $this->documentTask->process_id,
            'task_id' => $this->documentTask->id,
        ]);
        $job->handle();

        expect($this->documentTask->fresh()->status)->toBe('finished');

        Bus::assertDispatched(NotifyFinished::class, function ($job) {
            return $job->document->id === $this->document->id &&
                $job->jobName === $this->document->type->label() &&
                $job->user->id === $this->authUser->id;
        });

        Event::assertDispatched(ProcessFinished::class, function ($event) {
            return $event->documentTask->id === $this->documentTask->id;
        });
    });

    it('doesnt trigger notification when processing the job with silent flag', function () {
        Event::fake();
        expect($this->documentTask->status)->toBe('ready');
        $job = new RegisterFinishedProcess($this->document, [
            'process_id' => $this->documentTask->process_id,
            'task_id' => $this->documentTask->id,
            'silently' => true
        ]);
        $job->handle();

        expect($this->documentTask->fresh()->status)->toBe('finished');
        Bus::assertNotDispatched(NotifyFinished::class);
        Event::assertDispatched(ProcessFinished::class, function ($event) {
            return $event->documentTask->id === $this->documentTask->id;
        });
    });
});
