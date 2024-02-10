<?php

use App\Livewire\SocialMediaPost\TextBlock;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use Livewire\Livewire;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(TextBlock::class);

    $this->contentBlock = DocumentContentBlock::factory()->create([
        'document_id' => Document::factory()->create([
            'meta' => [
                'user_id' => $this->authUser->id
            ]
        ])
    ]);

    $this->component = Livewire::test(TextBlock::class, [
        'contentBlockId' => $this->contentBlock->id,
        'content' => 'Initial content',
        'customPrompt' => 'Custom prompt text',
        'faster' => false,
        'showCustomPrompt' => false,
        'processing' => false,
    ]);
});

describe(
    'Social Media TextBlock component',
    function () {
        it('renders the social media text block view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.social-media-post.text-block');
        });

        it('triggers text expansion', function () {
            $this->component->call('expand')
                ->assertSet('processing', true);
        });

        it('triggers text shorten', function () {
            $this->component->call('shorten')
                ->assertSet('processing', true);
        });

        it('runs a custom prompt', function () {
            $this->component->set('customPrompt', '')
                ->call('runCustomPrompt')
                ->assertHasErrors(['customPrompt' => 'required']);

            $this->component->set('customPrompt', 'some prompt')
                ->call('runCustomPrompt')
                ->assertSet('showCustomPrompt', false)
                ->assertSet('processing', true);
        });

        it('toggles custom prompt visibility', function () {
            $this->component->call('toggleCustomPrompt')
                ->assertSet('showCustomPrompt', true)
                ->call('toggleCustomPrompt')
                ->assertSet('showCustomPrompt', false);
        });

        it('updates content and processing state', function () {
            $newContent = 'Updated content';
            $this->contentBlock->content = $newContent;
            $this->contentBlock->save();

            $this->component->call('onProcessFinished', [
                'document_content_block_id' => $this->contentBlock->id
            ])->assertSet('content', $newContent)
                ->assertSet('processing', false);
        });
    }
)->group('social-media');
