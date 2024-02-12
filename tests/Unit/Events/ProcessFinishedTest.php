<?php

namespace Tests\Unit\Events;

use App\Events\ProcessFinished;
use App\Models\DocumentTask;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

describe('ProcessFinished event', function () {
    it('dispatches the event correctly', function () {
        Event::fake();
        $this->processId = Str::uuid();
        DocumentTask::factory()->create([
            'process_id' => $this->processId
        ]);

        event(new ProcessFinished($this->processId));

        Event::assertDispatched(ProcessFinished::class, function ($event) {
            return $event->documentTask->process_id === $this->processId->toString()
                && $event->groupFinished === false
                && $event->broadcastOn() instanceof PrivateChannel
                && $event->broadcastOn()->name === 'private-User.' . $event->documentTask->document->getMeta('user_id')
                && $event->broadcastWith() === [
                    'document_id' => $event->documentTask->document_id,
                    'parent_document_id' => null,
                    'process_id' => $this->processId->toString(),
                    'has_siblings' => false,
                    'process_group_id' => null,
                    'group_finished' => false,
                ] && $event->broadcastAs() === 'ProcessFinished';
        });
    });

    it('dispatches the event correctly with a process group', function () {
        Event::fake();
        $this->processId = Str::uuid();
        $this->processGroupId = Str::uuid();
        DocumentTask::factory()->create([
            'process_id' => $this->processId,
            'process_group_id' => $this->processGroupId
        ]);

        event(new ProcessFinished($this->processId));

        Event::assertDispatched(ProcessFinished::class, function ($event) {
            return $event->documentTask->process_id === $this->processId->toString()
                && $event->groupFinished === true
                && $event->broadcastOn() instanceof PrivateChannel
                && $event->broadcastOn()->name === 'private-User.' . $event->documentTask->document->getMeta('user_id')
                && $event->broadcastWith() === [
                    'document_id' => $event->documentTask->document_id,
                    'parent_document_id' => null,
                    'process_id' => $this->processId->toString(),
                    'has_siblings' => false,
                    'process_group_id' => $this->processGroupId->toString(),
                    'group_finished' => true,
                ] && $event->broadcastAs() === 'ProcessFinished';
        });
    });
})->group('events');
