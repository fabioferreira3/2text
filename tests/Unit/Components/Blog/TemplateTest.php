<?php

use App\Livewire\Blog\Template;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Template::class);
});

describe(
    'Blog Post component',
    function () {
        it('renders the blog post template view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.common.template')
                ->assertSet('icon', 'newspaper')
                ->assertSet('title', __('templates.blog_post'))
                ->assertSet('description', __('templates.create_blog_post'));
        });

        it('redirects to create new blog post', function () {
            $this->component->call('execute')->assertRedirect('/blog/new');
        });
    }
)->group('blog');
