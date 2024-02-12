<?php

use App\Livewire\AudioTranscription\AudioTranscription;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->be($this->authUser);
    $this->document = Document::factory()->create();
    $this->document->contentBlocks()->create([
        'type' => 'text',
        'content' => 'https://example.com/audio.mp3'
    ]);
    $this->component = actingAs($this->authUser)->livewire(AudioTranscription::class, [
        'document' => $this->document
    ]);
});

describe(
    'Audio Transcription component',
    function () {
        it('renders the audio transcription view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.audio-transcription.transcription')
                ->assertSet('title', $this->document->title)
                ->assertSet('isProcessing', false);
        });

        it('downloads subtitle in specified format', function () {
            $format = 'srt';
            $fileName = Str::random(10) . ".{$format}";
            $filePath = "subtitles/{$fileName}";

            Storage::fake('local');
            $file = UploadedFile::fake()->create($fileName, 100);
            Storage::put($filePath, $file->getContent());

            $this->document->updateMeta("{$format}_file_path", $filePath);

            $this->component->call('downloadSubtitle', $format)
                ->assertFileDownloaded($fileName);
        });
    }
)->group('audio-transcription');
