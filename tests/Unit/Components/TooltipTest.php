<?php

use App\Livewire\Tooltip;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Tooltip::class);
});

describe(
    'Tooltip component',
    function () {
        it('renders the tooltip view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.tooltip')
                ->assertSet('content', '');
        });
    }
);
