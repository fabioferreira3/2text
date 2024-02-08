<?php

namespace Tests\Unit\Events;

use App\Events\AudioGenerated;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Event;

describe('AudioGenerated event', function () {
    it('dispatches the audio generated event correctly', function () {
        Event::fake();

        $params = [
            'user_id' => 1,
            'process_id' => 123,
            'media_file_id' => 456
        ];

        AudioGenerated::dispatch($params);

        Event::assertDispatched(AudioGenerated::class, function ($event) use ($params) {
            return $event->params === $params
                && $event->broadcastOn() instanceof PrivateChannel
                && $event->broadcastOn()->name === 'private-User.1'
                && $event->broadcastWith() === [
                    'process_id' => 123,
                    'media_file_id' => 456
                ]
                && $event->broadcastAs() === 'AudioGenerated';
        });
    });
})->group('events');
