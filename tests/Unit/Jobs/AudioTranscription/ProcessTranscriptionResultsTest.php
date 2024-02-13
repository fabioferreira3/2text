<?php

use App\Enums\AIModel;
use App\Enums\DocumentTaskEnum;
use App\Jobs\AudioTranscription\ProcessTranscriptionResults;
use App\Jobs\DispatchDocumentTasks;
use App\Jobs\RegisterAppUsage;
use App\Models\Document;
use App\Models\DocumentTask;
use App\Repositories\MediaRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->document = Document::factory()->create([
        'meta' => [
            'duration' => 14
        ]
    ]);
    $this->documentTask = DocumentTask::factory()->create([
        'document_id' => $this->document->id,
        'process_id' => Str::uuid()
    ]);
    $this->transcriptId = Str::uuid();
    $mockMediaRepo = Mockery::mock(MediaRepository::class);
    $this->app->instance(MediaRepository::class, $mockMediaRepo);
    $mockMediaRepo->shouldReceive('getTranscription')
        ->andReturn([
            'text' => 'This is a test transcript.',
            'utterances' => [
                [
                    'text' => 'This is a test transcript.',
                    'speaker' => 'spk_0',
                ]
            ]
        ]);
    $mockMediaRepo->shouldReceive('getTranscriptionSubtitles')
        ->andReturn([
            'vtt_file_path' => 'path/to/vtt/file.vtt',
            'srt_file_path' => 'path/to/srt/file.srt'
        ]);
});

describe('AudioTranscription - ProcessTranscriptionResults job', function () {
    it('can be serialized', function () {
        $job = new ProcessTranscriptionResults($this->document, []);
        $serialized = serialize($job);
        expect($serialized)->toBeString();
    });

    it('process the transcription results', function () {
        $job = new ProcessTranscriptionResults($this->document, [
            'pending_task_id' => $this->documentTask->id,
            'transcript_id' => $this->transcriptId,
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        $this->document->refresh();
        expect($this->document->meta['context'])->toBe('This is a test transcript.');
        expect($this->document->meta['original_text'])->toBe('This is a test transcript.');
        expect($this->document->meta['transcript_id'])->toBe((string) $this->transcriptId);
        expect($this->document->meta['vtt_file_path'])->toBe('path/to/vtt/file.vtt');
        expect($this->document->meta['srt_file_path'])->toBe('path/to/srt/file.srt');

        $this->assertDatabaseHas('document_content_blocks', [
            'document_id' => $this->document->id,
            'type' => 'text',
            'content' => 'This is a test transcript.',
            'prefix' => 'Speaker spk_0',
            'prompt' => null,
            'order' => 1
        ]);

        Bus::assertNotDispatched(DispatchDocumentTasks::class);
        Bus::assertDispatched(RegisterAppUsage::class, function ($job) {
            return $job->account->id === $this->document->account_id
                && $job->params['meta']['document_id'] === $this->document->id
                && $job->params['meta']['document_task_id'] === $this->documentTask->id
                && $job->params['meta']['length'] === 14
                && $job->params['meta']['name'] === DocumentTaskEnum::PROCESS_TRANSCRIPTION_RESULTS->value
                && $job->params['length'] === 14
                && $job->params['model'] === AIModel::ASSEMBLY_AI->value;
        });

        expect($this->documentTask->fresh()->status)->toBe('finished');
    });

    it('registers a translation task when applicable', function () {
        $this->document->updateMeta('target_language', 'pt');
        $job = new ProcessTranscriptionResults($this->document, [
            'pending_task_id' => $this->documentTask->id,
            'transcript_id' => $this->transcriptId,
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        $this->assertDatabaseHas('document_tasks', [
            'document_id' => $this->document->id,
            'name' => DocumentTaskEnum::TRANSLATE_TEXT_BLOCK->value,
            'status' => 'ready',
            'order' => 1,
            'meta->target_language' => 'pt',
            'meta->content_block_id' => $this->document->contentBlocks()->first()->id
        ]);

        Bus::assertDispatched(DispatchDocumentTasks::class);
        Bus::assertDispatched(RegisterAppUsage::class);

        expect($this->documentTask->fresh()->status)->toBe('finished');
    });

    it('handles exception', function () {
        $mockMediaRepo = Mockery::mock(MediaRepository::class);
        $this->app->instance(MediaRepository::class, $mockMediaRepo);
        $mockMediaRepo->shouldReceive('getTranscription')
            ->once()
            ->andThrow(new Exception('Error'));
        $job = new ProcessTranscriptionResults($this->document, [
            'pending_task_id' => $this->documentTask->id,
            'transcript_id' => $this->transcriptId,
            'task_id' => $this->documentTask->id
        ]);
        $job->handle();

        Bus::assertNotDispatched(DispatchDocumentTasks::class);
        Bus::assertNotDispatched(RegisterAppUsage::class);

        expect($this->documentTask->fresh()->status)->toBe('failed');
    });
})->group('audio-transcription');
