<?php

use App\Livewire\Common\Sidebar;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Sidebar::class);
});

describe(
    'Sidebar component',
    function () {
        it('renders the sidebar view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.common.sidebar');
        });

        it('navigates to the dashboard', function () {
            $this->component->call('navigate', 'dashboard')
                ->assertSet('active', 'dashboard')
                ->assertRedirect(route('home'));
        });

        it('navigates to the tools page', function () {
            $this->component->call('navigate', 'templates')
                ->assertSet('active', 'templates')
                ->assertRedirect(route('tools'));
        });
    }
);
