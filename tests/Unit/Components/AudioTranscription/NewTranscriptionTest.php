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
                    ->assertHasErrors(['sourceUrl' => 'required'])
                    ->set('sourceUrl', fake()->url())
                    ->call('process')
                    ->assertHasNoErrors(['sourceUrl' => 'required'])
                    ->assertHasNoErrors(['sourceUrl' => 'url'])
                    ->assertHasErrors(['sourceUrl'])
                    ->set('sourceUrl', $this->youtubeUrl)
                    ->call('process')
                    ->assertHasNoErrors('sourceUrl');
            });

            test('origin language', function ($language) {
                $this->component
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('sourceUrl', $this->youtubeUrl)
                    ->set('originLanguage', '123')
                    ->call('process')
                    ->assertHasErrors(['originLanguage' => 'in'])
                    ->set('originLanguage', '')
                    ->call('process')
                    ->assertHasErrors(['originLanguage' => 'required'])
                    ->set('originLanguage', $language)
                    ->call('process')
                    ->assertHasNoErrors('originLanguage');
            })->with([Language::getValues()]);

            test('target language', function ($language) {
                $this->component
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('sourceUrl', $this->youtubeUrl)
                    ->set('targetLanguage', '123')
                    ->call('process')
                    ->assertHasErrors(['targetLanguage' => 'in'])
                    ->set('targetLanguage', '')
                    ->call('process')
                    ->assertHasErrors(['targetLanguage' => 'required'])
                    ->set('targetLanguage', $language)
                    ->call('process')
                    ->assertHasNoErrors('targetLanguage');
            })->with([Language::getValues()]);

            test('speakers expected', function ($language) {
                $this->component
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('sourceUrl', $this->youtubeUrl)
                    ->set('targetLanguage', $language)
                    ->set('identifySpeakers', true)
                    ->set('speakersExpected', null)
                    ->call('process')
                    ->assertHasErrors('speakersExpected');
            })->with([Language::getValues()]);
        });

        it('creates the document and dispatches the job', function () {
            Bus::fake(CreateTranscription::class);
            $this->component
                ->set('sourceType', SourceProvider::YOUTUBE->value)
                ->set('sourceUrl', $this->youtubeUrl)
                ->set('originLanguage', Language::ITALIAN->value)
                ->set('targetLanguage', Language::PORTUGUESE->value)
                ->set('identifySpeakers', true)
                ->set('speakersExpected', 4)
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
