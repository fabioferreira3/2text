<?php

use App\Enums\SourceProvider;
use App\Livewire\Blog\BlogPost;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->be($this->authUser);
    $repo = new DocumentRepository();
    $this->document = $repo->createBlogPost([
        'source' => SourceProvider::FREE_TEXT->value
    ]);
    $this->metaDescriptionBlock = $this->document->contentBlocks()->create([
        'type' => 'meta_description',
        'content' => 'Meta description here'
    ]);
    $this->document->flushWordCount();
    DocumentContentBlock::factory(3)->create([
        'document_id' => $this->document->id,
        'type' => 'text'
    ]);
    $this->document->flushWordCount();
    $this->component = actingAs($this->authUser)->livewire(BlogPost::class, [
        'document' => $this->document
    ]);
});

describe(
    'Blog Post component',
    function () {
        it('renders the blog post view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.blog.blog-post')
                ->assertSet('title', $this->document->title);
        });

        it('correctly defines title from document', function () {
            $component = actingAs($this->authUser)->livewire(BlogPost::class, [
                'document' => Document::factory()->create([
                    'title' => null
                ])
            ]);
            $component->call('defineTitle')
                ->assertSet('title', __('blog.blog_posts'));
        });

        it('updates document and flushes word count on block deletion', function () {
            $initialWordCount = $this->document->word_count;
            $this->document->contentBlocks()->ofTextType()->first()->delete();
            $this->component->call('blockDeleted');
            $this->document->refresh();

            $newWordCount = $this->document->word_count;
            expect($newWordCount)->toBeLessThan($initialWordCount);
        });

        it('copies post content to clipboard and dispatches alert', function () {
            $content = $this->document->getHtmlContentBlocksAsText();
            $this->component->call('copyPost')
                ->assertDispatched('addToClipboard', message: $content)
                ->assertDispatched('alert', type: 'info', message: __('alerts.copied_to_clipboard'));
        });
    }
)->group('blog');
