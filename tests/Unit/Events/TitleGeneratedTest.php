<?php

namespace Tests\Unit\Events;

use App\Events\TitleGenerated;
use App\Models\Document;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

describe('TitleGenerated event', function () {
    it('broadcasts title generated event correctly', function () {
        Event::fake();

        $this->document = Document::factory()->create();
        $this->processId = Str::uuid();

        event(new TitleGenerated($this->document, $this->processId));

        Event::assertDispatched(TitleGenerated::class, function ($event) {
            return $event->document->id === $this->document->id &&
                $event->processId === $this->processId->toString() &&
                $event->broadcastOn()->name === 'private-User.' . $this->document->getMeta('user_id') &&
                $event->broadcastWith() === [
                    'document_id' => $this->document->id,
                    'process_id' => $this->processId->toString(),
                ] &&
                $event->broadcastAs() === 'TitleGenerated';
        });
    });
})->group('events');
