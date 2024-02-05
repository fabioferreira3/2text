<?php

use App\Livewire\Summarizer\Dashboard;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'Summarizer Dashboard component',
    function () {
        test('renders the dashboard view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.summarizer.dashboard');
        });

        test('renders the new summarizer view', function () {
            $this->component->call('new')->assertRedirect('/summarizer/new');
        });
    }
)->group('summarizer');;
