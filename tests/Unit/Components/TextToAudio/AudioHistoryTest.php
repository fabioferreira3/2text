<?php

use App\Enums\DocumentType;
use App\Livewire\TextToAudio\AudioHistory;
use App\Models\Document;
use App\Models\MediaFile;
use App\Models\Voice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    $this->component = Livewire::test(AudioHistory::class);
});

describe('AudioHistory component', function () {
    it('mounts the component with default values', function () {
        $this->component
            ->assertSet('isPlaying', false)
            ->assertSet('displayHeader', true)
            ->assertSet('selectedMediaFile', null);
    });

    it('refreshes the audio history', function () {
        $voice = Voice::factory()->create();
        $document = Document::factory()->create([
            'type' => DocumentType::TEXT_TO_SPEECH,
            'content' => 'Sample content for testing',
            'meta' => ['voice_id' => $voice->id],
        ]);
        MediaFile::factory()->create([
            'account_id' => $document->account_id,
            'type' => 'audio',
            'meta' => ['document_id' => $document->id],
            'file_path' => 'audios/sample.mp3',
        ]);

        $this->component
            ->call('refresh')
            ->assertSet('history', function ($history) {
                return count($history) === 1 && Str::contains($history[0]['content'], 'Sample content');
            });
    });

    it('plays and stops audio', function () {
        $this->component
            ->call('playAudio', 1)
            ->assertSet('isPlaying', true)
            ->call('stopAudio')
            ->assertSet('isPlaying', false);
    });

    it('processes audio toggling between play and stop', function () {
        $component = Livewire::test(AudioHistory::class);

        $component->call('processAudio', 1)
            ->assertSet('isPlaying', true);

        $component->call('processAudio', 1)
            ->assertSet('isPlaying', false);
    });

    it('downloads a media file', function () {
        Storage::fake();
        $fakeFileContent = 'test content';
        $fakeFilePath = 'audios/sample.mp3';

        Storage::put($fakeFilePath, $fakeFileContent);

        $mediaFile = MediaFile::factory()->create(['file_path' => $fakeFilePath]);

        $this->component
            ->call('download', $mediaFile->id)
            ->assertFileDownloaded('sample.mp3', $fakeFileContent);
    });

    it('displays delete modal', function () {
        $mediaFile = MediaFile::factory()->create();
        $this->component
            ->call('displayDeleteModal', $mediaFile->id)
            ->assertSet('selectedMediaFile.id', $mediaFile->id);
    });

    it('deletes a media file', function () {
        $mediaFile = MediaFile::factory()->create();

        $this->component
            ->call('displayDeleteModal', $mediaFile->id)
            ->call('delete')
            ->assertSet('selectedMediaFile', null);

        expect($mediaFile->fresh()->deleted_at)->not->toBeNull();
    });

    it('aborts deletion', function () {
        $mediaFile = MediaFile::factory()->create();
        $this->component
            ->call('displayDeleteModal', $mediaFile->id)
            ->call('abortDeletion')
            ->assertSet('selectedMediaFile', null);
    });
})->group('text-to-audio');
