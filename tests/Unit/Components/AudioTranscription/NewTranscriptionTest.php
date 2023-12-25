<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Http\Livewire\AudioTranscription\NewTranscription;
use App\Jobs\AudioTranscription\CreateTranscription;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\{actingAs};
use function Pest\Faker\fake;

beforeEach(function () {
    $this->youtubeUrl = "https://www.youtube.com/watch?v=VG5gaPr1Mvs";
    $this->component = actingAs($this->authUser)->livewire(NewTranscription::class);
});

describe(
    'NewTranscription component',
    function () {
        it('renders the new correct view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.audio-transcription.new');
        });

        describe('component validation', function () {
            test('source', function () {
                $this->component
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->call('process')
                    ->assertHasNoErrors(['sourceType' => 'required'])
                    ->assertHasNoErrors(['sourceType' => 'in'])
                    ->set('sourceType', 'free_text')
                    ->call('process')
                    ->assertHasErrors(['sourceType' => 'in']);
            });

            test('source url', function () {
                $this->component
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->call('process')
                    ->assertHasErrors(['source_url' => 'required'])
                    ->set('source_url', fake()->url())
                    ->call('process')
                    ->assertHasNoErrors(['source_url' => 'required'])
                    ->assertHasNoErrors(['source_url' => 'url'])
                    ->assertHasErrors(['source_url'])
                    ->set('source_url', $this->youtubeUrl)
                    ->call('process')
                    ->assertHasNoErrors('source_url');
            });

            test('origin language', function ($language) {
                $this->component
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('source_url', $this->youtubeUrl)
                    ->set('origin_language', '123')
                    ->call('process')
                    ->assertHasErrors(['origin_language' => 'in'])
                    ->set('origin_language', '')
                    ->call('process')
                    ->assertHasErrors(['origin_language' => 'required'])
                    ->set('origin_language', $language)
                    ->call('process')
                    ->assertHasNoErrors('origin_language');
            })->with([Language::getValues()]);

            test('target language', function ($language) {
                $this->component
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('source_url', $this->youtubeUrl)
                    ->set('target_language', '123')
                    ->call('process')
                    ->assertHasErrors(['target_language' => 'in'])
                    ->set('target_language', '')
                    ->call('process')
                    ->assertHasErrors(['target_language' => 'required'])
                    ->set('target_language', $language)
                    ->call('process')
                    ->assertHasNoErrors('target_language');
            })->with([Language::getValues()]);
        });

        it('creates the document and dispatches the job', function () {
            Bus::fake(CreateTranscription::class);
            $this->component
                ->set('sourceType', SourceProvider::YOUTUBE->value)
                ->set('source_url', $this->youtubeUrl)
                ->set('origin_language', Language::ITALIAN->value)
                ->set('target_language', Language::PORTUGUESE->value)
                ->set('identify_speakers', true)
                ->set('speakers_expected', 4)
                ->call('process')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('documents', [
                'type' => DocumentType::AUDIO_TRANSCRIPTION->value,
                'language' => Language::ITALIAN->value,
                'meta->source' => SourceProvider::YOUTUBE->value,
                'meta->source_url' => $this->youtubeUrl,
                'meta->target_language' => Language::PORTUGUESE->label(),
                'meta->identify_speakers' => true,
                'meta->speakers_expected' => 4,
            ]);

            Bus::assertDispatched(CreateTranscription::class);
        });
    }
)->group('audio-transcription');
