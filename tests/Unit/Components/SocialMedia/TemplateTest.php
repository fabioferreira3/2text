<?php

use App\Enums\DocumentType;
use App\Enums\Tone;
use App\Livewire\SocialMediaPost\Template;
use App\Models\Document;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Template::class);
});

describe(
    'Social Media Template component',
    function () {
        it('renders the social media template view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.common.template');
        });

        it('redirects to create new social media', function () {
            $this->component->call('execute');
            $this->assertDatabaseHas('documents', [
                'type' => DocumentType::SOCIAL_MEDIA_GROUP,
                'language' => 'en',
                'meta->source' => null,
                'meta->context' => null,
                'meta->source_url' => null,
                'meta->tone' => Tone::DEFAULT->value,
                'meta->style' => null,
                'meta->keyword' => null,
                'meta->more_instructions' => null,
            ]);
            $document = Document::where('type', DocumentType::SOCIAL_MEDIA_GROUP)->first();
            $this->component->assertRedirect(route('social-media-view', ['document' => $document]));
        });
    }
)->group('social-media');
