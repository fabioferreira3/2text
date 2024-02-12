<?php

use App\Enums\DocumentStatus;
use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Jobs\DispatchDocumentTasks;
use App\Livewire\TextToAudio\TextToAudio;
use App\Models\Document;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Livewire\Livewire;


beforeEach(function () {
    $this->be($this->authUser);
    $this->component = Livewire::test(TextToAudio::class);
});

describe(
    'Text to Audio Template component',
    function () {
        it('mounts with default values', function () {
            $this->component
                ->assertSet('isProcessing', false)
                ->assertSet('isPlaying', false)
                ->assertSet('selectedVoice', null)
                ->assertSet('processId', '')
                ->assertSet('document', null);
        });

        it('validates input', function () {
            $this->component
                ->call('generate')
                ->assertHasErrors(['inputText' => 'required', 'selectedVoice' => 'required']);
        });

        it('processes text to audio generation as expected', function () {
            Bus::fake();

            $this->authUser->account->update(['units' => 99999]);
            $voiceId = (string) Str::uuid();

            $this->component
                ->set('inputText', 'Test input text')
                ->set('selectedVoice', $voiceId)
                ->call('generate')
                ->assertSet('isProcessing', true)
                ->assertSet('processId', function ($processId) {
                    return Str::isUuid($processId);
                });

            $this->assertDatabaseHas('documents', [
                'id' => $this->component->document->id,
                'title' => '',
                'language' => Language::ENGLISH->value,
                'type' => DocumentType::TEXT_TO_SPEECH->value,
                'content' => 'Test input text',
                'meta->voice_id' => $voiceId,
                'meta->source' => SourceProvider::FREE_TEXT->value
            ]);

            $this->assertDatabaseHas('document_tasks', [
                'document_id' => $this->component->document->id,
                'job' => DocumentTaskEnum::TEXT_TO_AUDIO->getJob(),
                'status' => DocumentStatus::READY->value,
                'meta->voice_id' => $voiceId,
                'meta->input_text' => 'Test input text'
            ]);

            Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) {
                return $job->document->id === $this->component->document->id;
            });
        });

        it('fails to process text to audio generation with insufficient units', function () {
            Bus::fake();

            $this->authUser->account->update(['units' => 0]);
            $voiceId = (string) Str::uuid();

            $this->component
                ->set('inputText', 'Test input text')
                ->set('selectedVoice', $voiceId)
                ->call('generate')
                ->assertDispatched('alert', type: 'error', message: __('alerts.insufficient_units'));

            $this->assertDatabaseMissing('documents', [
                'title' => '',
                'language' => Language::ENGLISH->value,
                'type' => DocumentType::TEXT_TO_SPEECH->value,
                'content' => 'Test input text',
                'meta->voice_id' => $voiceId,
                'meta->source' => SourceProvider::FREE_TEXT->value
            ]);

            $this->assertDatabaseMissing('document_tasks', [
                'job' => DocumentTaskEnum::TEXT_TO_AUDIO->getJob(),
                'status' => DocumentStatus::READY->value,
                'meta->voice_id' => $voiceId,
                'meta->input_text' => 'Test input text'
            ]);

            Bus::assertNotDispatched(DispatchDocumentTasks::class);
        });

        it('plays audio', function () {
            $audioId = 'test-audio-id';
            $this->component
                ->call('playAudio', $audioId)
                ->assertSet('isPlaying', true)
                ->call('stopAudio')
                ->assertSet('isPlaying', false);
        });

        it('processes finished event and updates the component state', function () {
            $mediaFile = MediaFile::factory()->create(['file_path' => 'audio/test.mp3']);
            $processId = (string) Str::uuid();

            $this->component
                ->set('processId', $processId)
                ->call('onProcessFinished', ['process_id' => $processId, 'media_file_id' => $mediaFile->id])
                ->assertSet('isProcessing', false)
                ->assertSet('currentAudioFile.id', $mediaFile->id)
                ->assertSet('currentAudioUrl', $mediaFile->getSignedUrl());
        });

        it('mounts the component with a document preloading input text', function () {
            $document = Document::factory()->create(['content' => 'Document content']);

            Livewire::test(TextToAudio::class, ['document' => $document->id])
                ->assertSet('inputText', 'Document content');
        });
    }
)->group('text-to-audio');
