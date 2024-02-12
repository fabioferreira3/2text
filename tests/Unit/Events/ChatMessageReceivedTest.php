<?php

namespace Tests\Unit\Events;

use App\Events\ChatMessageReceived;
use App\Models\ChatThreadIteration;
use Illuminate\Support\Facades\Event;

describe('ChatMessageReceived event', function () {
    it('broadcasts chat message received event', function () {
        Event::fake();

        $iteration = ChatThreadIteration::factory()->create();

        event(new ChatMessageReceived($iteration));

        Event::assertDispatched(ChatMessageReceived::class, function ($event) use ($iteration) {
            return $event->iteration->id === $iteration->id &&
                $event->broadcastOn()->name === 'private-User.' . $iteration->thread->user_id &&
                $event->broadcastAs() === 'ChatMessageReceived' &&
                $event->broadcastWith() === [
                    'chat_thread_id' => $iteration->thread->id,
                    'message' => $iteration->response
                ];
        });
    });
})->group('events');
