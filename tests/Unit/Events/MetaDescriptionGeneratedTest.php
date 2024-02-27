<?php

use App\Events\MetaDescriptionGenerated;
use App\Models\Document;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

describe(
    'MetaDescriptionGenerated event',
    function () {
        it('broadcasts the event correctly', function () {
            Event::fake();

            $this->processId = Str::uuid();
            $this->document = Document::factory()->create();
            event(new MetaDescriptionGenerated($this->document, $this->processId));

            Event::assertDispatched(MetaDescriptionGenerated::class, function ($event) {
                return $event->document->id === $this->document->id &&
                    $event->processId === (string) $this->processId &&
                    $event->broadcastOn()->name === 'private-User.' . $this->document->getMeta('user_id') &&
                    $event->broadcastWith() === [
                        'document_id' => $this->document->id,
                        'process_id' => (string) $this->processId,
                    ] &&
                    $event->broadcastAs() === 'MetaDescriptionGenerated';
            });
        });
    }
)->group('events');
