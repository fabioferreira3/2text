<?php

namespace Tests\Unit\Events;

use App\Enums\DocumentTaskEnum;
use App\Events\InsufficientUnitsValidated;
use App\Models\Document;
use Illuminate\Support\Facades\Event;

describe('InsufficientUnitsValidated event', function () {
    it('broadcasts on the correct channel with correct data', function () {
        Event::fake();

        $document = Document::factory()->create();
        $taskName = DocumentTaskEnum::CREATE_TITLE->value;

        broadcast(new InsufficientUnitsValidated($document, $taskName));

        Event::assertDispatched(InsufficientUnitsValidated::class, function ($event) use ($document, $taskName) {
            return $event->document->id === $document->id &&
                $event->taskName === $taskName &&
                $event->broadcastOn()->name === 'private-User.' . $document->getMeta('user_id') &&
                $event->broadcastWith() === [
                    'document_id' => $document->id,
                    'task' => $taskName,
                ] &&
                $event->broadcastAs() === 'InsufficientUnitsValidated';
        });
    });
})->group('events');
