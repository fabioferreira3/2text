<?php

use App\Events\ContentBlockUpdated;
use App\Models\DocumentContentBlock;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

describe(
    'ContentBlockUpdated event',
    function () {
        it('broadcasts the event correctly', function () {
            Event::fake();

            $this->processId = Str::uuid();
            $this->contentBlock = DocumentContentBlock::factory()->create();
            event(new ContentBlockUpdated($this->contentBlock, $this->processId));

            Event::assertDispatched(ContentBlockUpdated::class, function ($event) {
                return $event->contentBlock->id === $this->contentBlock->id &&
                    $event->processId === (string) $this->processId &&
                    $event->broadcastOn()->name === 'private-User.' . $this->contentBlock->document->getMeta('user_id') &&
                    $event->broadcastWith() === [
                        'document_content_block_id' => $this->contentBlock->id,
                        'process_id' => (string) $this->processId,
                    ] &&
                    $event->broadcastAs() === 'ContentBlockUpdated';
            });
        });
    }
)->group('events');
