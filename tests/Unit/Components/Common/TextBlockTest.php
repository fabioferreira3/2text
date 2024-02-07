<?php

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Livewire\Common\Blocks\TextBlock;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\DocumentContentBlockVersion;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->authUser->account->update(['units' => 99999]);
    $this->contentBlock = DocumentContentBlock::factory()->create([
        'content' => 'some content',
        'document_id' => Document::factory()->create(['account_id' => $this->authUser->account_id])
    ]);
    $this->contentBlock->versions()->save(
        new DocumentContentBlockVersion([
            'content' => 'future content 1',
            'version' => 2,
            'active' => false
        ])
    );
    $this->contentBlock->versions()->save(
        new DocumentContentBlockVersion([
            'content' => 'future content 2',
            'version' => 3,
            'active' => false
        ])
    );
    $this->component = actingAs($this->authUser)->livewire(TextBlock::class, [
        'contentBlock' => $this->contentBlock
    ]);
});

describe(
    'TextBlock component',
    function () {
        it('renders the text block component', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.common.blocks.text-block');
        });

        it('triggers a rewrite text block task register', function () {
            Bus::fake();
            $this->component->call('rewrite', 'prompt here');
            $this->component->assertSet('processing', true);

            $this->assertDatabaseHas('document_tasks', [
                'name' => DocumentTaskEnum::REWRITE_TEXT_BLOCK->value,
                'job' => DocumentTaskEnum::REWRITE_TEXT_BLOCK->getJob(),
                'document_id' => $this->contentBlock->document_id,
                'meta->document_content_block_id' => $this->contentBlock->id,
                'meta->text' => 'some content',
                'meta->prompt' => 'prompt here',
                'order' => 1
            ]);

            Bus::assertDispatched(DispatchDocumentTasks::class);
        });

        it('throws insufficient units error', function () {
            $this->authUser->account->update(['units' => 0]);
            $this->component->call('rewrite', 'prompt here')
                ->assertDispatched('alert', type: 'error', message: __('alerts.insufficient_units'));

            $this->authUser->account->update(['units' => 5]);
            $this->component->call('rewrite', 'prompt here')
                ->assertNotDispatched('alert', type: 'error', message: __('alerts.insufficient_units'));
        });

        it('deletes its content block', function () {
            $this->component->call('delete')
                ->assertDispatched('blockDeleted')
                ->assertDispatched(
                    'alert',
                    type: 'success',
                    message: __('alerts.text_block_removed')
                );
            $this->assertDatabaseMissing(
                'document_content_blocks',
                ['id' => $this->contentBlock->id]
            );
        });

        it('copies the content to the clipboard', function () {
            $this->component->call('copy')
                ->assertDispatched('addToClipboard', message: 'some content')
                ->assertDispatched(
                    'alert',
                    type: 'info',
                    message: __('alerts.text_copied')
                );
        });

        it('undo the content version', function () {
            $this->component->call('redo');
            $this->component->call('undo')
                ->assertDispatched('adjustTextArea');
            $this->assertEquals($this->contentBlock->fresh()->content, 'some content');
        });

        it('redo the content version', function () {
            $this->component->call('redo')
                ->assertDispatched('adjustTextArea');
            $this->assertEquals($this->contentBlock->fresh()->content, 'future content 1');

            $this->component->call('redo')
                ->assertDispatched('adjustTextArea');
            $this->assertEquals($this->contentBlock->fresh()->content, 'future content 2');
        });
    }
)->group('common');
