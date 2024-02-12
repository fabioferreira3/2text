<?php

use App\Livewire\Summarizer\SummaryView;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use Illuminate\View\ViewException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->document = Document::factory()->create([
        'title' => 'Doc title',
        'account_id' => $this->authUser->account_id,
        'meta' => [
            'tone' => 'default',
            'source' => 'source',
            'context' => 'context',
            'user_id' => $this->authUser->id,
        ]
    ]);
    $this->contentBlock = DocumentContentBlock::factory()->create([
        'document_id' => $this->document->id,
        'type' => 'text',
        'content' => 'content',
    ]);

    $this->component = actingAs($this->authUser)->livewire(SummaryView::class, [
        'document' => $this->document,
    ]);
});

describe(
    'SummaryView component',
    function () {
        it('renders the summary view page', function () {
            $this->component->assertStatus(200)
                ->assertSet('document', $this->document)
                ->assertSet('title', 'Doc title')
                ->assertSet('source', 'source')
                ->assertSet('context', 'context')
                ->assertSet('contentBlock.id', $this->contentBlock->id)
                ->assertViewIs('livewire.summarizer.summary-view');
        });

        it('renders the summary view page with nullable data', function () {
            $document = Document::factory()->create([
                'title' => null,
                'account_id' => $this->authUser->account_id,
                'content' => 'some content',
                'meta' => [
                    'tone' => 'default',
                    'source' => 'source',
                    'user_id' => $this->authUser->id,
                ]
            ]);
            $contentBlock = DocumentContentBlock::factory()->create([
                'document_id' => $document->id,
                'type' => 'text',
                'content' => 'content',
            ]);
            $component = actingAs($this->authUser)->livewire(SummaryView::class, [
                'document' => $document,
            ]);
            $component->assertStatus(200)
                ->assertSet('document', $document)
                ->assertSet('title', __('summarizer.summary'))
                ->assertSet('source', 'source')
                ->assertSet('context', 'some content')
                ->assertSet('contentBlock.id', $contentBlock->id);
        });

        it('throws an error if there is no content block', function () {
            $document = Document::factory()->create([
                'title' => null,
                'account_id' => $this->authUser->account_id,
                'content' => 'some content',
                'meta' => [
                    'source' => 'source',
                    'user_id' => $this->authUser->id,
                ]
            ]);
            $this->expectException(ViewException::class);
            actingAs($this->authUser)->livewire(SummaryView::class, [
                'document' => $document,
            ]);
        });

        it('redirects to new summarizer', function () {
            $this->component->call('new')
                ->assertRedirect(route('new-summarizer'));
        });
    }
)->group('summarizer');
