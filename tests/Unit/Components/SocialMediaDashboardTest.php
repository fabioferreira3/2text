<?php

use App\Http\Livewire\SocialMediaPost\Dashboard;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'Social Media Dashboard component',
    function () {
        test('renders the dashboard view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.social-media-post.dashboard');
        });

        test('renders the new social media manager view', function () {
            $this->component->call('new')->assertRedirect('/social-media-post/new');
        });
    }
);
