<?php

use App\Livewire\Paraphraser\Dashboard;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'Paraphraser Dashboard component',
    function () {
        test('renders the dashboard view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.paraphraser.dashboard');
        });

        test('renders the new paraphraser view', function () {
            $this->component->call('new')->assertRedirect('/paraphraser/new');
        });
    }
)->group('paraphraser');
