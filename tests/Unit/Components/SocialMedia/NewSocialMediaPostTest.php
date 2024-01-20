<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\Tone;
use App\Http\Livewire\SocialMediaPost\NewSocialMediaPost;
use App\Models\Document;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->component = actingAs($this->authUser)->livewire(NewSocialMediaPost::class);
});

describe(
    'NewSocialMediaPost component',
    function () {
        it('creates a social media group document and redirects', function () {
            $this->assertEquals($this->component->document->type, DocumentType::SOCIAL_MEDIA_GROUP);
            $this->assertEquals($this->component->document->account_id, $this->authUser->account_id);
            $this->assertEquals($this->component->document->getMeta('source'), null);
            $this->assertEquals($this->component->document->getMeta('context'), null);
            $this->assertEquals($this->component->document->language, Language::ENGLISH);
            $this->assertEquals($this->component->document->getMeta('source_url'), null);
            $this->assertEquals($this->component->document->getMeta('tone'), Tone::DEFAULT->value);
            $this->assertEquals($this->component->document->getMeta('style'), null);
            $this->assertEquals($this->component->document->getMeta('keyword'), null);
            $this->assertEquals($this->component->document->getMeta('more_instructions'), null);

            $this->component->assertRedirect('/documents/social-media/' . $this->component->document->id);
        });
    }
)->group('social-media');
