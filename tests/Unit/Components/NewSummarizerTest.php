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
});

describe(
    'NewSummarizer component',
    function () {
        test('renders the new correct view', function () {
            actingAs($this->authUser)
                ->livewire(NewSummarizer::class)
                ->assertStatus(200)
                ->assertViewIs('livewire.summarizer.new');
        });

        test('redirects to document view once finished processing', function () {
            actingAs($this->authUser)
                ->livewire(NewSummarizer::class)
                ->set('document', $this->document)
                ->call('onProcessFinished', ['document_id' => $this->document->id])
                ->assertRedirect(route('summary-view', ['document' => $this->document]));
        });

        describe('NewSummarizer component validation', function () {
            test('context', function () {
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
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
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
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
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
                    ->set('document', $this->document)
                    ->set('source', 'youtube')
                    ->call('process')
                    ->set('sourceUrl', $url)
                    ->assertHasErrors('sourceUrl');
            })->with([fake()->url(), fake()->url(), fake()->url(), fake()->url()]);

            test('youtube valid source url', function (string $url) {
                dump($url);
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
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
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
                    ->set('document', $this->document)
                    ->set('maxWordsCount', '49')
                    ->call('process')
                    ->assertHasErrors(['maxWordsCount' => 'min'])
                    ->set('maxWordsCount', '3001')
                    ->call('process')
                    ->assertHasErrors(['maxWordsCount' => 'max']);
            });

            test('fileInput is required for specific source types', function () {
                $sourceTypes = ['docx', 'pdf_file', 'csv'];
                foreach ($sourceTypes as $sourceType) {
                    actingAs($this->authUser)
                        ->livewire(NewSummarizer::class)
                        ->set('document', $this->document)
                        ->set('source', $sourceType)
                        ->set('fileInput', null)
                        ->call('process')
                        ->assertHasErrors(['fileInput' => 'required_if']);
                }
            });

            test('fileInput must be a valid docx file', function () {
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
                    ->set('document', $this->document)
                    ->set('source', 'docx')
                    ->set('fileInput', UploadedFile::fake()->create('avatar.txt'))
                    ->call('process')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if'])
                    ->set('fileInput', UploadedFile::fake()->create('avatar.docx'))
                    ->call('process')
                    ->assertHasNoErrors('fileInput');
            });

            test('fileInput must be a valid pdf file', function () {
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
                    ->set('document', $this->document)
                    ->set('source', 'pdf_file')
                    ->set('fileInput', UploadedFile::fake()->create('avatar.txt'))
                    ->call('process')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if']);
            });

            test('fileInput must be a valid csv file', function () {
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
                    ->set('document', $this->document)
                    ->set('source', 'csv')
                    ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                    ->call('process')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if']);
            });

            test('target language', function () {
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
                    ->set('document', $this->document)
                    ->set('targetLanguage', null)
                    ->call('process')
                    ->assertHasErrors(['targetLanguage' => 'required'])
                    ->set('targetLanguage', 'maio')
                    ->call('process')
                    ->assertHasErrors(['targetLanguage' => 'in']);
            });

            test('source language', function () {
                actingAs($this->authUser)
                    ->livewire(NewSummarizer::class)
                    ->set('document', $this->document)
                    ->set('sourceLanguage', null)
                    ->call('process')
                    ->assertHasErrors(['sourceLanguage' => 'required'])
                    ->set('sourceLanguage', 'maio')
                    ->call('process')
                    ->assertHasErrors(['sourceLanguage' => 'in']);
            });
        });

        test('store file', function () {
            $response = actingAs($this->authUser)
                ->livewire(NewSummarizer::class)
                ->set('document', $this->document)
                ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                ->call('storeFile');

            Storage::disk('s3')->assertExists($response->filePath);
        });

        test('process', function () {
            actingAs($this->authUser)
                ->livewire(NewSummarizer::class)
                ->set('source', SourceProvider::FREE_TEXT->value)
                ->set('context', 'eita porra')
                ->set('maxWordsCount', 250)
                ->call('process')
                ->assertHasNoErrors();

            $this->assertDatabaseHas('documents', [
                'type' => DocumentType::SUMMARIZER->value,
                'content' => 'eita porra',
                'meta->source' => SourceProvider::FREE_TEXT->value,
                'meta->max_words_count' => 250
            ]);

            Bus::assertDispatched(PrepareCreationTasks::class);
        });
    }
);
