<?php

use App\Http\Livewire\Summarizer\NewSummarizer;
use App\Models\Document;
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
        });
    }
);
