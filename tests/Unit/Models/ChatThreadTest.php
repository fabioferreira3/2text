<?php

use App\Models\ChatThread;
use App\Models\User;
use App\Models\ChatThreadIteration;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Assuming you have a UserFactory and DocumentFactory set up
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->document = Document::factory()->create();
});

it('belongs to a user', function () {
    $chatThread = ChatThread::factory()->create(['user_id' => $this->user->id]);
    expect($chatThread->user)->toBeInstanceOf(User::class);
    expect($chatThread->user->id)->toBe($this->user->id);
});

it('has many chat thread iterations', function () {
    $chatThread = ChatThread::factory()->create(['user_id' => $this->user->id]);
    $iterations = ChatThreadIteration::factory()->count(3)->create(['chat_thread_id' => $chatThread->id]);
    expect($chatThread->iterations)->toHaveCount(3);
    $chatThread->iterations->each(function ($item) use ($iterations) {
        expect($iterations->pluck('id'))->toContain($item->id);
    });
});

it('can belong to a document', function () {
    $chatThread = ChatThread::factory()->create([
        'user_id' => $this->user->id,
        'document_id' => $this->document->id
    ]);
    expect($chatThread->document)->toBeInstanceOf(Document::class);
    expect($chatThread->document->id)->toBe($this->document->id);
});

it('applies a global scope to only show threads of the authenticated user', function () {
    $otherUser = User::factory()->create();
    ChatThread::factory()->create(['user_id' => $this->user->id]);
    ChatThread::factory()->create(['user_id' => $otherUser->id]);

    $threads = ChatThread::all();
    expect($threads)->toHaveCount(1);
    expect($threads->first()->user_id)->toBe($this->user->id);
});

it('automatically assigns the authenticated user id when creating a new thread', function () {
    $chatThread = ChatThread::create(['name' => 'Test Thread']);
    expect($chatThread->user_id)->toBe($this->user->id);
});

it('can query threads that are not document related', function () {
    ChatThread::factory()->count(2)->create(['user_id' => $this->user->id, 'document_id' => null]);
    ChatThread::factory()->create(['user_id' => $this->user->id, 'document_id' => $this->document->id]);

    $threads = ChatThread::notDocumentRelated()->get();
    expect($threads)->toHaveCount(2);
    $threads->each(function ($thread) {
        expect($thread->document_id)->toBeNull();
    });
});
