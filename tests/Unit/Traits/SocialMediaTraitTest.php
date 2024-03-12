<?php

namespace Tests\Unit\Models\Traits;

use App\Livewire\SocialMediaPost\Platforms\FacebookPost;
use App\Livewire\SocialMediaPost\Platforms\InstagramPost;
use App\Livewire\SocialMediaPost\Platforms\LinkedinPost;
use App\Livewire\SocialMediaPost\Platforms\TwitterPost;
use App\Models\Document;
use App\Models\User;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Mockery;

beforeEach(function () {
    $this->user = User::factory()->create();
    Auth::shouldReceive('user')->andReturn($this->user);
    Auth::shouldReceive('check')->andReturn(true);
    $this->mockDocRepo = Mockery::mock(new DocumentRepository());
});

describe('SocialMediaTrait', function () {
    it('mounts', function ($trait) {
        $document = Document::factory()->create([
            'meta' => [
                'user_id' => $this->user->id
            ]
        ]);

        $trait->mount($document);

        $this->assertFalse($trait->saving);
        $this->assertEquals($trait->userId, $this->user->id);
        $this->assertEquals($document, $trait->document);
        $this->assertFalse($trait->showImageGenerator);
        $this->assertNull($trait->image);
        $this->assertNull($trait->imageBlock);
        $this->assertNull($trait->imageBlockId);
        $this->assertEquals($trait->imagePrompt, '');
        $this->assertEquals($trait->text, '');
        $this->assertNull($trait->textBlockId);
    })->with([
        new FacebookPost(),
        new InstagramPost(),
        new LinkedinPost(),
        new TwitterPost()
    ]);

    it('gets listeners', function ($trait) {
        $listeners = $trait->getListeners();

        $this->assertIsArray($listeners);
        $this->assertContains('textBlockUpdated', $listeners);
        $this->assertContains('contentBlockUpdated', $listeners);
    })->with([
        new FacebookPost(),
        new InstagramPost(),
        new LinkedinPost(),
        new TwitterPost()
    ]);

    it('saves', function () {
        $this->mockDocRepo->shouldReceive('updateContentBlock')->withArgs(function ($arg1, $arg2) {
            return $arg1 == 1 && $arg2['content'] == 'Sample text';
        })->andReturn([]);
        $this->app->instance(DocumentRepository::class, $this->mockDocRepo);
        $trait = new FacebookPost();
        $trait->textBlockId = 1;
        $trait->text = 'Sample text';
        $trait->save();

        $this->assertFalse($trait->saving);
    });
})->group('traits');
