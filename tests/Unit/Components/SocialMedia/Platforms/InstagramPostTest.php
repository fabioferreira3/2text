<?php

use App\Livewire\SocialMediaPost\Platforms\InstagramPost;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use Illuminate\Support\Str;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->contentBlock = DocumentContentBlock::factory()->create([
        'document_id' => $this->document->id,
        'type' => 'text',
        'content' => 'This is a test content'
    ]);
    $this->imageBlock = DocumentContentBlock::factory()->create([
        'document_id' => $this->document->id,
        'type' => 'media_file_image',
        'content' => Str::uuid(),
        'prompt' => 'Image prompt'
    ]);
    $this->component = actingAs($this->authUser)->livewire(InstagramPost::class, [
        'document' => $this->document
    ]);
});

describe(
    'Instagram Post component',
    function () {
        it('renders the facebook post view', function () {
            $this->component->assertSet('document', $this->document);
            $this->component->assertSet('userId', $this->authUser->id);
            $this->component->assertSet('saving', false);
            $this->component->assertSet('showImageGenerator', false);
            $this->component->assertSet('text', 'This is a test content');
            $this->component->assertSet('textBlockId', $this->contentBlock->id);
            $this->component->assertSet('imageBlockId', $this->imageBlock->id);
            $this->component->assertSet('imagePrompt', 'Image prompt');
            $this->component->assertStatus(200)->assertViewIs('livewire.social-media-post.platforms.instagram-post');
        });
    }
)->group('social-media');
