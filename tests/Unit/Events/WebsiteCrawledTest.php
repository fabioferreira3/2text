<?php

use App\Events\WebsiteCrawled;
use App\Models\Document;
use Illuminate\Support\Facades\Event;

describe(
    'WebsiteCrawled event',
    function () {
        it('broadcasts the event correctly', function () {
            Event::fake();

            $this->document = Document::factory()->create();
            event(new WebsiteCrawled($this->document->getMeta('user_id')));

            Event::assertDispatched(WebsiteCrawled::class, function ($event) {
                return $event->userId === $this->document->getMeta('user_id') &&
                    $event->broadcastOn()->name === 'private-User.' . $this->document->getMeta('user_id') &&
                    $event->broadcastAs() === 'WebsiteCrawled';
            });
        });
    }
)->group('events');
