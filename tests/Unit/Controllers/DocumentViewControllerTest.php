<?php

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->be($this->authUser);
});

describe('DocumentViewController controller', function () {
    it('redirects to the correct route for a BlogPost document in progress', function () {
        $document = Document::factory()->create([
            'type' => DocumentType::BLOG_POST
        ]);
        $document->tasks()->save(new DocumentTask([
            'name' => 'Title',
            'job' => 'App\Jobs\BroadcastCustomEvent',
            'process_id' => Str::uuid(),
            'status' => DocumentStatus::IN_PROGRESS
        ]));

        $response = $this->get(route('document-view', ['document' => $document]));
        $response->assertRedirect(route('blog-post-processing-view', ['document' => $document]));
    });

    it('redirects to the correct route for a BlogPost document that is a draft', function () {
        $document = Document::factory()->create([
            'type' => DocumentType::BLOG_POST,
        ]);
        $response = $this->get(route('document-view', ['document' => $document]));
        $response->assertRedirect(route('blog-post-processing-view', ['document' => $document]));
    });

    it('redirects to the correct route for a BlogPost document that is published', function () {
        $document = Document::factory()->create([
            'type' => DocumentType::BLOG_POST
        ]);
        $document->tasks()->save(new DocumentTask([
            'name' => 'Title',
            'job' => 'App\Jobs\BroadcastCustomEvent',
            'process_id' => Str::uuid(),
            'status' => DocumentStatus::FINISHED
        ]));

        $response = $this->get(route('document-view', ['document' => $document]));
        $response->assertRedirect(route('blog-post-view', ['document' => $document]));
    });

    it('redirects to the correct route for a non-BlogPost document', function ($type, $route) {
        $document = Document::factory()->create([
            'type' => $type,
        ]);

        $response = $this->get(route('document-view', ['document' => $document]));

        $response->assertRedirect(route($route, ['document' => $document]));
    })->with([
        ['type' => DocumentType::SOCIAL_MEDIA_GROUP, 'route' => 'social-media-view'],
        ['type' => DocumentType::PARAPHRASED_TEXT, 'route' => 'paraphrase-view'],
        ['type' => DocumentType::TEXT_TO_SPEECH, 'route' => 'text-to-audio-view'],
        ['type' => DocumentType::AUDIO_TRANSCRIPTION, 'route' => 'transcription-view'],
        ['type' => DocumentType::INQUIRY, 'route' => 'insight-view']
    ]);
})->group('controllers');
