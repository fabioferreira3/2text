<?php

use App\Livewire\Blog\Dashboard;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'Blog Post Dashboard component',
    function () {
        test('renders the dashboard view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.blog.dashboard');
        });

        test('renders the new blog post view', function () {
            $this->component->call('new')->assertRedirect('/blog/new');
        });
    }
)->group('blog');
