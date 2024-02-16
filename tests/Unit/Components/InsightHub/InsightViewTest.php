<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Livewire\InsightHub\InsightView;
use App\Jobs\InsightHub\PrepareTasks;
use App\Models\ChatThread;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\{actingAs};
use function Pest\Faker\fake;

beforeEach(function () {
    $this->authUser->account->update(['units' => 99999]);
    $this->document = Document::factory()->create([
        'type' => DocumentType::INQUIRY->value,
        'account_id' => $this->authUser->account_id
    ]);
    ChatThread::create([
        'document_id' => $this->document->id,
        'user_id' => $this->authUser->id
    ]);
    $this->component = actingAs($this->authUser)->livewire(InsightView::class, [
        'document' => $this->document
    ]);
});

describe(
    'InsightView component',
    function () {
        test('renders the new correct view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.insight-hub.insight-view');
        });

        test('confirms embedding', function () {
            $this->component
                ->assertSet('hasEmbeddings', false)
                ->call('onEmbeddingFinished', ['document_id' => $this->document->id])
                ->assertSet('hasEmbeddings', true)
                ->assertSet('isProcessing', false)
                ->assertSet('context', null)
                ->assertSet('sourceUrl', null)
                ->assertSet('fileInput', null)
                ->assertSet('videoLanguage', Language::ENGLISH->value)
                ->assertSet('sourceType', SourceProvider::FREE_TEXT->value);

            $this->document->refresh();
            $this->assertTrue($this->document->getMeta('has_embeddings'));
        });

        describe('InsightView component validation', function () {
            test('context', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('context', fake()->text(30500))
                    ->set('sourceType', 'free_text')
                    ->call('embed')
                    ->assertHasErrors(['context' => 'max'])
                    ->assertHasNoErrors(['context' => 'required_if'])
                    ->set('context', null)
                    ->call('embed')
                    ->assertHasErrors(['context' => 'required_if']);
            });

            test('source url', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', 'website_url')
                    ->call('embed')
                    ->assertHasErrors(['sourceUrl' => 'required'])
                    ->set('sourceUrl', fake()->url())
                    ->call('embed')
                    ->assertHasNoErrors(['sourceUrl' => 'required'])
                    ->set('sourceUrl', 'a not url string')
                    ->call('embed')
                    ->assertHasErrors(['sourceUrl' => 'url'])
                    ->set('source', 'youtube')
                    ->call('embed')
                    ->set('sourceUrl', fake()->url())
                    ->assertHasErrors('sourceUrl');
            });

            test('youtube invalid source url', function (string $url) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', 'youtube')
                    ->set('sourceUrl', $url)
                    ->call('embed')
                    ->assertHasErrors('sourceUrl');
            })->with([fake()->url(), fake()->url(), fake()->url(), fake()->url()]);

            test('youtube valid source url', function (string $url) {
                $this->component
                    ->set('document', $this->document)
                    ->set('source', 'youtube')
                    ->set('context', '')
                    ->set('sourceUrl', $url)
                    ->call('embed')
                    ->assertHasNoErrors('sourceUrl');
            })->with([
                "https://www.youtube.com/watch?v=Co5B66dJghw",
                "https://www.youtube.com/watch?v=VG5gaPr1Mvs",
                "https://www.youtube.com/watch?v=e6z3aSxxc2k&t=1202s"
            ]);

            test('source file path is updated when embedding files', function ($sourceType) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', $sourceType)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                    ->call('embed')
                    ->assertHasNoErrors();

                $this->document->refresh();
                $this->assertNotNull($this->document->getMeta('source_file_path'));
                Bus::assertDispatched(PrepareTasks::class);
            })->with([SourceProvider::PDF->value]);

            test('fileInput is required for specific source types', function ($sourceType) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', $sourceType)
                    ->call('embed')
                    ->assertHasErrors(['fileInput' => 'required_if']);
            })->with([SourceProvider::DOCX->value, SourceProvider::PDF->value, SourceProvider::CSV->value]);

            test('fileInput must be a valid docx file', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::DOCX->value)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.txt'))
                    ->call('embed')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if'])
                    ->set('fileInput', UploadedFile::fake()->create('avatar.docx'))
                    ->call('embed')
                    ->assertHasNoErrors('fileInput');
            });

            test('fileInput must be a valid pdf file', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::PDF->value)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.txt'))
                    ->call('embed')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if']);
            });

            test('fileInput must be a valid csv file', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::CSV->value)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                    ->call('embed')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if']);
            });

            test('invalid video language', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('videoLanguage', null)
                    ->call('embed')
                    ->assertHasErrors(['videoLanguage' => 'required_if'])
                    ->set('videoLanguage', 'maio')
                    ->call('embed')
                    ->assertHasErrors(['videoLanguage' => 'in']);
            });

            test('valid video language', function ($language) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('videoLanguage', $language)
                    ->call('embed')
                    ->assertHasNoErrors(['videoLanguage' => 'in']);
            })->with(Language::getValues());
        });

        test('store file', function () {
            $response = $this->component
                ->set('document', $this->document)
                ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'));

            Storage::disk('s3')->assertExists($response->filePath);
        });

        test('embed', function () {
            $this->component
                ->set('source', SourceProvider::FREE_TEXT->value)
                ->set('context', 'any context')
                ->call('embed')
                ->assertHasNoErrors()
                ->assertSet('isProcessing', true);

            Bus::assertDispatched(PrepareTasks::class);
        });
    }
)->group('insight');
