<?php

use App\Enums\DocumentType;
use App\Livewire\SocialMediaPost\NewSocialMediaPost;
use App\Models\Document;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->authUser->account->update(['units' => 99999]);
    $this->document = Document::factory()->create(['account_id' => $this->authUser->account_id]);
    $this->component = actingAs($this->authUser)->livewire(NewSocialMediaPost::class);
});

describe(
    'NewSocialMediaPost component',
    function () {
        it('creates a social media group document and redirects', function () {
            $document = Document::where('type', DocumentType::SOCIAL_MEDIA_GROUP)->first();
            $this->component->assertRedirect('/documents/social-media/' . $document->id);
        });
    }
)->group('social-media');
