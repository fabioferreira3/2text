<?php

use App\Livewire\Dashboard;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'Dashboard component',
    function () {
        it('renders the main dashboard view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.dashboard')
                ->assertSet('title', 'Dashboard')
                ->assertSet('tab', 'dashboard');
        });

        it('renders the main dashboard view based on tab', function () {
            actingAs($this->authUser)->livewire(Dashboard::class, [
                'tab' => 'images'
            ])->assertStatus(200)
                ->assertViewIs('livewire.dashboard')
                ->assertSet('title', __('dashboard.ai_images'))
                ->assertSet('tab', 'images');

            actingAs($this->authUser)->livewire(Dashboard::class, [
                'tab' => 'audio'
            ])->assertStatus(200)
                ->assertViewIs('livewire.dashboard')
                ->assertSet('title', __('dashboard.my_audios'))
                ->assertSet('tab', 'audio');

            actingAs($this->authUser)->livewire(Dashboard::class, [
                'tab' => 'something-else'
            ])->assertStatus(200)
                ->assertViewIs('livewire.dashboard')
                ->assertSet('title', __('dashboard.dashboard'))
                ->assertSet('tab', 'something-else');
        });

        it('triggers events when updating tab', function () {
            $this->component
                ->set('tab', 'images')
                ->assertDispatched('titleUpdated', title: __('dashboard.ai_images'));

            $this->component
                ->set('tab', 'audio')
                ->assertDispatched('titleUpdated', title: __('dashboard.my_audios'));

            $this->component
                ->set('tab', 'something-else')
                ->assertDispatched('titleUpdated', title: __('dashboard.dashboard'));
        });

        // it('navigates to the tools page', function () {
        //     $this->component->call('navigate', 'templates')
        //         ->assertSet('active', 'templates')
        //         ->assertRedirect(route('tools'));
        // });
    }
);
