<?php

namespace Tests\Unit\Events;

use App\Events\Paraphraser\TextParaphrased;
use App\Models\Document;
use Illuminate\Support\Facades\Event;

describe('TextParaphrased event', function () {
    it('dispatches TextParaphrased event correctly', function () {
        Event::fake();

        $document = Document::factory()->create();
        $params = [
            'user_id' => 1,
            'process_id' => 'abc123',
        ];

        event(new TextParaphrased($document, $params));

        Event::assertDispatched(TextParaphrased::class, function ($event) use ($document, $params) {
            return $event->document->id === $document->id &&
                $event->params['user_id'] === $params['user_id'] &&
                $event->params['process_id'] === $params['process_id'];
        });

        Event::assertDispatched(TextParaphrased::class, function (TextParaphrased $event) {
            return $event->broadcastOn()->name === 'private-User.1' &&
                $event->broadcastWith() === [
                    'document_id' => $event->document->id,
                    'process_id' => 'abc123',
                    'user_id' => 1,
                ] &&
                $event->broadcastAs() === 'TextParaphrased';
        });
    });
})->group('events');
