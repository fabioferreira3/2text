<?php

use App\Livewire\Templates;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Templates::class);
});

describe(
    'General Templates component',
    function () {
        it('renders the blog post template view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.templates');
        });
    }
);
