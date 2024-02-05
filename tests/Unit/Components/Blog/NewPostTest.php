<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Enums\Style;
use App\Enums\Tone;
use App\Livewire\Blog\NewPost;
use App\Jobs\Blog\PrepareCreationTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Cloudinary\Transformation\Source;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(NewPost::class);
});

describe(
    'Blog - New Post component',
    function () {
        test('renders the dashboard view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.blog.new');
        })->group('blog');

        describe(
            'component validation',
            function () {
                test('context', function () {
                    $this->component
                        ->set('context', '')
                        ->call('process')
                        ->assertHasErrors(['context' => 'required'])
                        ->assertSee(__('validation.blog_post_context_required'))
                        ->set('context', 'some context')
                        ->call('process')
                        ->assertHasNoErrors(['context' => 'required'])
                        ->set('context', fake()->text(30500))
                        ->call('process')
                        ->assertHasErrors(['context' => 'max']);
                });

                test('source urls', function ($source) {
                    $this->component
                        ->set('source', $source)
                        ->set('sourceUrls', [fake()->url()])
                        ->call('process')
                        ->assertHasNoErrors(['sourceUrls' => 'required_if'])
                        ->set('sourceUrls', [])
                        ->call('process')
                        ->assertHasErrors(['sourceUrls' => 'required_if'])
                        ->assertSee(__('validation.blog_post_sourceurl_required'))
                        ->set('sourceUrls', ['not a url'])
                        ->call('process')
                        ->assertHasErrors('sourceUrls.*');

                    if ($source === SourceProvider::YOUTUBE->value) {
                        $this->component->set('sourceUrls', [
                            fake()->url(), fake()->url(), fake()->url(), fake()->url()
                        ])
                            ->call('process')
                            ->assertHasErrors(['sourceUrls']);
                    }

                    if ($source === SourceProvider::WEBSITE_URL->value) {
                        $this->component->set('sourceUrls', [
                            fake()->url(),
                            fake()->url(),
                            fake()->url(),
                            fake()->url(),
                            fake()->url(),
                            fake()->url(),
                            fake()->url()
                        ])->call('process')->assertHasErrors(['sourceUrls']);
                    }
                })->with([
                    SourceProvider::YOUTUBE->value,
                    SourceProvider::WEBSITE_URL->value
                ]);

                test('source', function ($source) {
                    $this->component
                        ->set('source', 'invalid source')
                        ->call('process')
                        ->assertHasErrors(['source' => 'in'])
                        ->set('source', $source)
                        ->call('process')
                        ->assertHasNoErrors(['source' => 'in']);
                })->with([SourceProvider::getValues()]);

                test('keyword', function () {
                    $this->component
                        ->set('keyword', '')
                        ->call('process')
                        ->assertHasErrors(['keyword' => 'required'])
                        ->assertSee(__('validation.keyword_required'))
                        ->set('keyword', 'some keyword')
                        ->call('process')
                        ->assertHasNoErrors('keyword');
                });

                test('language', function ($language) {
                    $this->component
                        ->set('language', '')
                        ->call('process')
                        ->assertHasErrors(['language' => 'required'])
                        ->assertSee(__('validation.language_required'))
                        ->set('language', 'invalid')
                        ->call('process')
                        ->assertHasErrors(['language' => 'in'])
                        ->set('language', $language)
                        ->call('process')
                        ->assertHasNoErrors('language');
                })->with([Language::getValues()]);

                test('target headers count', function ($number) {
                    $this->component
                        ->set('targetHeadersCount', '')
                        ->call('process')
                        ->assertHasErrors(['targetHeadersCount' => 'required'])
                        ->set('targetHeadersCount', 'invalid')
                        ->call('process')
                        ->assertHasErrors(['targetHeadersCount' => 'numeric'])
                        ->set('targetHeadersCount', 1)
                        ->call('process')
                        ->assertHasErrors(['targetHeadersCount' => 'min'])
                        ->assertSee(__('validation.min_subtopics', ['min' => 2]))
                        ->set('targetHeadersCount', 11)
                        ->call('process')
                        ->assertHasErrors(['targetHeadersCount' => 'max'])
                        ->assertSee(__('validation.max_subtopics', ['max' => 10]))
                        ->set('targetHeadersCount', $number)
                        ->call('process')
                        ->assertHasNoErrors(['targetHeadersCount' => 'max']);
                })->with([2, 3, 4, 5, 6, 7, 8, 9]);

                test('tone', function ($tone) {
                    $this->component
                        ->set('tone', '')
                        ->call('process')
                        ->assertHasNoErrors('tone')
                        ->set('tone', 'invalid')
                        ->call('process')
                        ->assertHasErrors(['tone' => 'in'])
                        ->set('tone', $tone)
                        ->call('process')
                        ->assertHasNoErrors('tone');
                })->with([Tone::getValues()]);

                test('style', function ($style) {
                    $this->component
                        ->set('style', '')
                        ->call('process')
                        ->assertHasNoErrors('style')
                        ->set('style', 'invalid')
                        ->call('process')
                        ->assertHasErrors(['style' => 'in'])
                        ->set('style', $style)
                        ->call('process')
                        ->assertHasNoErrors('style');
                })->with([Style::getValues()]);

                test('fileInput', function ($source, $file) {
                    $this->component
                        ->set('source', $source)
                        ->set('fileInput', UploadedFile::fake()->create($file))
                        ->call('process')
                        ->assertHasNoErrors('fileInput');
                })->with([
                    [SourceProvider::PDF->value, 'avatar.pdf'],
                    [SourceProvider::DOCX->value, 'avatar.docx'],
                    [SourceProvider::CSV->value, 'avatar.csv']
                ]);

                test('image prompt', function () {
                    $this->component
                        ->set('generateImage', true)
                        ->set('imgPrompt', '')
                        ->call('process')
                        ->assertHasErrors(['imgPrompt' => 'required_if'])
                        ->assertSee(__('validation.img_prompt_required'))
                        ->set('generateImage', false)
                        ->set('imgPrompt', '')
                        ->call('process')
                        ->assertHasNoErrors('imgPrompt');
                });
            }
        )->group('blog');

        it('processes a new post', function () {
            Bus::fake(PrepareCreationTasks::class);
            $this->component
                ->set('source', SourceProvider::FREE_TEXT->value)
                ->set('sourceUrls', ['https://experior.ai', 'https://sub.experior.ai'])
                ->set('context', 'some context')
                ->set('keyword', 'some keyword')
                ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                ->set('generateImage', true)
                ->set('imgPrompt', 'some img prompt')
                ->set('targetHeadersCount', 5)
                ->set('language', Language::PORTUGUESE->value)
                ->call('process')
                ->assertHasNoErrors();

            $document = Document::latest()->first();
            $this->component->assertRedirect(route('blog-post-processing-view', ['document' => $document]));

            expect($document->getMeta('source_file_path'))->toBeTruthy();

            $this->assertDatabaseHas('documents', [
                'type' => DocumentType::BLOG_POST->value,
                'language' => Language::PORTUGUESE->value,
                'meta->source' => SourceProvider::FREE_TEXT->value,
                'meta->source_urls' => json_encode(['https://experior.ai', 'https://sub.experior.ai']),
                'meta->target_headers_count' => 5,
                'meta->context' => 'some context',
                'meta->tone' => 'default',
                'meta->style' => 'default',
                'meta->keyword' => 'some keyword',
                'meta->img_prompt' => 'some img prompt',
                'meta->generate_image' => true
            ]);

            Bus::assertDispatched(PrepareCreationTasks::class);
        })->group('blog');
    }
);
