<?php

use App\Enums\DocumentStatus;
use App\Enums\SourceProvider;
use App\Livewire\Blog\ProcessingBlogPost;
use App\Models\DocumentTask;
use App\Repositories\DocumentRepository;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->be($this->authUser);
    $repo = new DocumentRepository();
    $this->document = $repo->createBlogPost([
        'source' => SourceProvider::FREE_TEXT->value
    ]);
    $this->component = actingAs($this->authUser)->livewire(ProcessingBlogPost::class, [
        'document' => $this->document
    ]);
});

describe(
    'ProcessingBlogPost component',
    function () {
        it('renders the blog post processing view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.blog.blog-post-processing')
                ->assertSet('title', __('oraculum.oraculum_is_working'))
                ->assertSet('currentThought', __('oraculum.where_to_start'))
                ->assertSet('thoughts', null)
                ->assertSet('currentProgress', 7);
        });

        it('redirects to blog post view page if status is finished', function () {
            DocumentTask::factory()->create([
                'document_id' => $this->document->id,
                'status' => DocumentStatus::FINISHED
            ]);
            $this->component->call('checkStatus')
                ->assertRedirect(route('blog-post-view', [
                    'document' => $this->document
                ]));
        });
    }
)->group('blog');
