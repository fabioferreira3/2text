<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Livewire\Paraphraser\NewParaphraser;
use App\Jobs\Paraphraser\CreateFromWebsite;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->document = Document::factory()->create();
    $this->component = actingAs($this->authUser)->livewire(NewParaphraser::class);
});

describe(
    'NewParaphraser component',
    function () {
        it('renders the right view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.paraphraser.new');
        });

        it('creates a document and redirects to it', function () {
            $this->assertEquals($this->component->sourceType, SourceProvider::FREE_TEXT->value);
            $this->component->call('start');
            $document = Document::first();
            expect($document->type)->toBe(DocumentType::PARAPHRASED_TEXT);
            expect($document->language)->toBe(Language::ENGLISH);
            expect($document->meta['source'])->toBe(SourceProvider::FREE_TEXT->value);
            expect($document->meta['tone'])->toBe(null);
            expect($document->meta['source_url'])->toBe(null);

            expect($this->component->document->id)->toBe($document->id);

            $this->component->assertRedirect(route('paraphrase-view', ['document' => $document]));
        });

        it('validates the component', function () {
            $this->component->set('sourceType', SourceProvider::PDF->value);
            $this->component->call('start');
            $this->component->assertHasErrors(['sourceType' => 'in']);
            $this->component->set('sourceType', SourceProvider::WEBSITE_URL->value);
            $this->component->call('start');
            $this->component->assertHasErrors(['sourceUrl' => 'required_if']);
            $this->component->set('sourceUrl', fake()->url());
            $this->component->call('start');
            $this->component->assertHasNoErrors();
        });

        it('dispatches the create from website job', function ($sourceType) {
            Bus::fake(CreateFromWebsite::class);
            $this->component->set('sourceType', $sourceType);
            $this->component->set('sourceUrl', fake()->url());
            $this->component->call('start');

            if ($sourceType === SourceProvider::WEBSITE_URL->value) {
                $this->component->assertSet('isProcessing', true);
                $this->component->assertDispatchedBrowserEvent('alert');
                Bus::assertDispatched(CreateFromWebsite::class, function ($job) {
                    return $job->document->id === $this->component->document->id;
                });
            } else {
                $this->component->assertSet('isProcessing', false);
                $this->component->assertNotDispatchedBrowserEvent('alert');
                Bus::assertNotDispatched(CreateFromWebsite::class);
            }
        })->with([
            SourceProvider::FREE_TEXT->value,
            SourceProvider::WEBSITE_URL->value
        ]);
    }
)->group('paraphraser');
