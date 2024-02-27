<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Livewire\InsightHub\Dashboard;
use App\Models\Document;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'InsightHub - Dashboard component',
    function () {
        it('renders the dashboard view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.insight-hub.dashboard');
        });

        it('creates a new insight', function () {
            $this->component->call('new');

            $this->assertDatabaseHas('documents', [
                'type' => DocumentType::INQUIRY->value,
                'language' => Language::ENGLISH->value,
                'account_id' => $this->authUser->account_id
            ]);

            $this->assertDatabaseHas('chat_thread_iterations', [
                'origin' => 'sys',
                'response' => "Hi " . auth()->user()->name . "! I just read your source. What would you like to know about it?"
            ]);

            $document = Document::where('type', DocumentType::INQUIRY->value)->first();
            $this->component->assertRedirect(route('insight-view', ['document' => $document]));
        });
    }
)->group('insight');
