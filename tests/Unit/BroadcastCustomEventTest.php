<?php

use App\Events\BroadcastEvent;
use App\Jobs\BroadcastCustomEvent;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

it('broadcasts a custom event', function () {

    Bus::fake([BroadcastCustomEvent::class]);
    Event::fake([BroadcastEvent::class]);

    $user = User::factory()->create();
    $document = Document::factory()->create([
        'meta' => [
            'user_id' => $user->id
        ]
    ]);
    $job = new BroadcastCustomEvent($document, ['event_name' => 'CustomEventName']);
    $job->handle();

    Event::assertDispatched(BroadcastEvent::class, function ($event) use ($document, $user) {
        return $event->params['event_name'] === 'CustomEventName'
            && $event->params['payload']['document_id'] === $document->id
            && $event->params['user_id'] === $user->id;
    });
});
