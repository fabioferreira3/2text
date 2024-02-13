<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\Language;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\InsightHub\RegisterEmbedVideoStream;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create();
});

describe('Insight Hub - RegisterEmbedVideoStream job', function () {
    it('can be serialized', function () {
        $job = new RegisterEmbedVideoStream($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('registers the embed task', function ($language) {
        $url = fake()->url();
        Bus::fake([DispatchDocumentTasks::class]);
        $processId = Str::uuid();
        $job = new RegisterEmbedVideoStream($this->document, [
            'process_id' => $processId,
            'source_url' => $url,
            'video_language' => $language
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::DOWNLOAD_SUBTITLES->value,
            'job' => DocumentTaskEnum::DOWNLOAD_SUBTITLES->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $processId,
            'meta->video_language' => $language,
            'meta->source_url' => $url,
            'meta->embed_source' => true,
            'order' => 1
        ]);

        $this->assertDatabaseHas('document_tasks', [
            'name' => DocumentTaskEnum::TRANSCRIBE_AUDIO->value,
            'job' => DocumentTaskEnum::TRANSCRIBE_AUDIO->getJob(),
            'document_id' => $this->document->id,
            'process_id' => $processId,
            'meta->abort_when_context_present' => true,
            'meta->embed_source' => true,
            'order' => 2
        ]);
    })->with(Language::getValues());
})->group('insight-hub');
