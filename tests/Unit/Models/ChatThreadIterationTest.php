<?php

use App\Models\ChatThread;
use App\Models\ChatThreadIteration;

it('belongs to a chat thread', function () {
    $chatThread = ChatThread::factory()->create();

    $chatThreadIteration = ChatThreadIteration::factory()->create([
        'chat_thread_id' => $chatThread->id,
    ]);

    $this->assertTrue($chatThreadIteration->thread->is($chatThread));
});
