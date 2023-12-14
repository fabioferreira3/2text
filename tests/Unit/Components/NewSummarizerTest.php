<?php

use App\Enums\DocumentType;
use App\Enums\SourceProvider;
use App\Http\Livewire\Summarizer\NewSummarizer;
use App\Jobs\Summarizer\PrepareCreationTasks;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\{actingAs};
use function Pest\Faker\fake;

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->component = actingAs($this->authUser)->livewire(NewSummarizer::class);
});

describe(
    'NewSummarizer component',
    function () {
        test('renders the new correct view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.summarizer.new');
        });

        test('redirects to document view once finished processing', function () {
            $this->component->set('document', $this->document)
                ->call('onProcessFinished', ['document_id' => $this->document->id])
                ->assertRedirect(route('summary-view', ['document' => $this->document]));
        });

        describe('NewSummarizer component validation', function () {
            test('context', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('context', fake()->text(30500))
                    ->set('source', 'free_text')
                    ->call('process')
                    ->assertHasErrors(['context' => 'max'])
                    ->assertHasNoErrors(['context' => 'required_if'])
                    ->set('context', null)
                    ->call('process')
                    ->assertHasErrors(['context' => 'required_if']);
            });

            test('source url', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('source', 'website_url')
                    ->call('process')
                    ->assertHasErrors(['sourceUrl' => 'required_if'])
                    ->set('sourceUrl', fake()->url())
                    ->call('process')
                    ->assertHasNoErrors(['sourceUrl' => 'required_if'])
                    ->set('sourceUrl', 'a not url string')
                    ->call('process')
                    ->assertHasErrors(['sourceUrl' => 'url'])
                    ->set('source', 'youtube')
                    ->call('process')
                    ->set('sourceUrl', fake()->url())
                    ->assertHasErrors('sourceUrl');
            });

            test('youtube invalid source url', function (string $url) {
                $this->component
                    ->set('document', $this->document)
                    ->set('source', 'youtube')
                    ->call('process')
                    ->set('sourceUrl', $url)
                    ->assertHasErrors('sourceUrl');
            })->with([fake()->url(), fake()->url(), fake()->url(), fake()->url()]);

            test('youtube valid source url', function (string $url) {
                $this->component
                    ->set('document', $this->document)
                    ->set('source', 'youtube')
                    ->set('context', '')
                    ->set('sourceUrl', $url)
                    ->call('process')
                    ->assertHasNoErrors('sourceUrl');
            })->with([
                "https://www.youtube.com/watch?v=Co5B66dJghw",
                "https://www.youtube.com/watch?v=VG5gaPr1Mvs",
                "https://www.youtube.com/watch?v=e6z3aSxxc2k&t=1202s"
            ]);

            test('max words count', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('maxWordsCount', '49')
                    ->call('process')
                    ->assertHasErrors(['maxWordsCount' => 'min'])
                    ->set('maxWordsCount', '601')
                    ->call('process')
                    ->assertHasErrors(['maxWordsCount' => 'max']);
            });

            test('fileInput is required for specific source types', function () {
                $sourceTypes = ['docx', 'pdf_file', 'csv'];
                foreach ($sourceTypes as $sourceType) {
                    $this->component
                        ->set('document', $this->document)
                        ->set('source', $sourceType)
                        ->set('fileInput', null)
                        ->call('process')
                        ->assertHasErrors(['fileInput' => 'required_if']);
                }
            });

            test('fileInput must be a valid file', function ($source, $file) {
                $this->component
                    ->set('document', $this->document)
                    ->set('source', $source)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.mp3'))
                    ->call('process')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if'])
                    ->set('fileInput', UploadedFile::fake()->create($file))
                    ->call('process')
                    ->assertHasNoErrors('fileInput');
            })->with([
                [SourceProvider::PDF->value, 'avatar.pdf'],
                [SourceProvider::DOCX->value, 'avatar.docx'],
                [SourceProvider::CSV->value, 'avatar.csv']
            ]);

            test('target language', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('targetLanguage', null)
                    ->call('process')
                    ->assertHasErrors(['targetLanguage' => 'required'])
                    ->set('targetLanguage', 'maio')
                    ->call('process')
                    ->assertHasErrors(['targetLanguage' => 'in']);
            });

            test('source language', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceLanguage', null)
                    ->call('process')
                    ->assertHasErrors(['sourceLanguage' => 'required'])
                    ->set('sourceLanguage', 'maio')
                    ->call('process')
                    ->assertHasErrors(['sourceLanguage' => 'in']);
            });
        });

        test('333store file', function () {
            $response = $this->component
                ->set('document', $this->document)
                ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                ->call('storeFile');

            Storage::disk('s3')->assertExists($response->filePath);
        });

        test('process', function () {
            $this->component
                ->set('source', SourceProvider::FREE_TEXT->value)
                ->set('context', 'any context')
                ->set('maxWordsCount', 250)
                ->call('process')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('documents', [
                'type' => DocumentType::SUMMARIZER->value,
                'content' => 'any context',
                'meta->source' => SourceProvider::FREE_TEXT->value,
                'meta->max_words_count' => 250
            ]);

            Bus::assertDispatched(PrepareCreationTasks::class);
        });
    }
);
