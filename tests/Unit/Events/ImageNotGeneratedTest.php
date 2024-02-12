<?php

namespace Tests\Unit\Events;

use App\Events\ImageNotGenerated;
use App\Models\DocumentTask;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

describe('ImageNotGenerated event', function () {
    it('broadcasts image not generated event', function () {
        Event::fake();

        $this->processId = Str::uuid();
        $documentTask = DocumentTask::factory()->create([
            'process_id' => $this->processId,
        ]);

        event(new ImageNotGenerated($this->processId));

        Event::assertDispatched(ImageNotGenerated::class, function ($event) use ($documentTask) {
            return $event->documentTask->id === $documentTask->id &&
                $event->broadcastOn()->name === 'private-User.' . $documentTask->document->getMeta('user_id') &&
                $event->broadcastAs() === 'ImageNotGenerated' &&
                $event->broadcastWith() === [
                    'document_id' => $documentTask->document_id,
                    'parent_document_id' => null,
                    'process_id' => $this->processId->toString(),
                    'process_group_id' => null
                ];
        });
    });
})->group('events');
