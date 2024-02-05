<?php

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Enums\Style;
use App\Enums\Tone;
use App\Livewire\SocialMediaPost\SocialMediaPostsManager;
use App\Jobs\SocialMedia\ProcessSocialMediaPosts;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\{actingAs};
use function Pest\Faker\fake;

beforeEach(function () {
    $this->document = Document::factory()->create([
        'type' => DocumentType::SOCIAL_MEDIA_POST->value
    ]);
    $this->component = actingAs($this->authUser)->livewire(SocialMediaPostsManager::class);
});

describe(
    'SocialMediaPostsManager component',
    function () {
        test('renders the new correct view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.social-media-post.posts-manager');
        });

        describe('SocialMediaPostsManager component validation', function () {
            test('context', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', 'free_text')
                    ->set('context', fake()->text(30500))
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
                    ->set('sourceType', 'website_url')
                    ->call('process')
                    ->assertHasErrors(['sourceUrls' => 'required_if'])
                    ->set('sourceUrls', [fake()->url()])
                    ->call('process')
                    ->assertHasNoErrors(['sourceUrls' => 'required_if'])
                    ->set('sourceUrls', ['a not url string'])
                    ->call('process')
                    ->assertHasErrors('sourceUrls.*')
                    ->set('sourceType', 'youtube')
                    ->call('process')
                    ->set('sourceUrls', [fake()->url()])
                    ->assertHasErrors('sourceUrls');
            });

            test('youtube invalid source url', function (string $url) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', 'youtube')
                    ->set('tempSourceUrl', $url)
                    ->call('addSourceUrl')
                    ->assertHasErrors('tempSourceUrl');
            })->with([fake()->url(), fake()->url(), fake()->url()]);

            test('youtube valid source url', function (string $url) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', 'youtube')
                    ->set('tempSourceUrl', $url)
                    ->call('addSourceUrl')
                    ->assertHasNoErrors('tempSourceUrl');
            })->with([
                "https://www.youtube.com/watch?v=Co5B66dJghw",
                "https://www.youtube.com/watch?v=VG5gaPr1Mvs",
                "https://www.youtube.com/watch?v=e6z3aSxxc2k&t=1202s"
            ]);

            test('source file path is updated when embedding files', function ($sourceType) {
                $this->component
                    ->set('document', $this->document)
                    ->set('platforms', [
                        "Linkedin" => false,
                        "Facebook" => true,
                        "Instagram" => false,
                        "Twitter" => false
                    ])
                    ->set('keyword', 'test')
                    ->set('sourceType', $sourceType)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                    ->call('process')
                    ->assertHasNoErrors();

                $this->document->refresh();
                $this->assertNotNull($this->document->getMeta('source_file_path'));
                Bus::assertDispatched(ProcessSocialMediaPosts::class);
            })->with([SourceProvider::PDF->value]);

            test('fileInput is required for specific source types', function ($sourceType) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', $sourceType)
                    ->set('platforms', [
                        "Linkedin" => false,
                        "Facebook" => true,
                        "Instagram" => false,
                        "Twitter" => false
                    ])
                    ->set('keyword', 'test')
                    ->call('process')
                    ->assertHasErrors(['fileInput']);
            })->with([SourceProvider::DOCX->value, SourceProvider::PDF->value, SourceProvider::CSV->value]);


            test('fileInput must be a valid docx file', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::DOCX->value)
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
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::PDF->value)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.txt'))
                    ->call('process')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if'])
                    ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                    ->call('process')
                    ->assertHasNoErrors('fileInput');
            });

            test('fileInput must be a valid csv file', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::CSV->value)
                    ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                    ->call('process')
                    ->assertHasErrors('fileInput')
                    ->assertHasNoErrors(['fileInput' => 'max'])
                    ->assertHasNoErrors(['fileInput' => 'required_if'])
                    ->set('fileInput', UploadedFile::fake()->create('avatar.csv'))
                    ->call('process')
                    ->assertHasNoErrors('fileInput');
            });

            test('invalid language', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('language', '')
                    ->call('process')
                    ->assertHasErrors(['language' => 'required'])
                    ->set('language', 'maio')
                    ->call('process')
                    ->assertHasErrors(['language' => 'in']);
            });

            test('valid language', function ($language) {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('language', $language)
                    ->call('process')
                    ->assertHasNoErrors(['language' => 'in']);
            })->with(Language::getValues());

            test('invalid image prompt', function () {
                $this->component
                    ->set('document', $this->document)
                    ->set('sourceType', SourceProvider::YOUTUBE->value)
                    ->set('generateImage', true)
                    ->call('process')
                    ->assertHasErrors(['imgPrompt' => 'required_if'])
                    ->set('imgPrompt', 'image prompt')
                    ->call('process')
                    ->assertHasNoErrors('imgPrompt');
            });
        });

        test('store file', function () {
            $response = $this->component
                ->set('document', $this->document)
                ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'));

            Storage::disk('s3')->assertExists($response->filePath);
        });

        test('process with free text', function () {
            $this->component
                ->set('sourceType', SourceProvider::FREE_TEXT->value)
                ->set('context', 'any context')
                ->set('platforms', [
                    "Linkedin" => false,
                    "Facebook" => true,
                    "Instagram" => false,
                    "Twitter" => false
                ])
                ->set('keyword', '123')
                ->call('process')
                ->assertHasNoErrors()
                ->assertSet('generating', true);

            Bus::assertDispatched(ProcessSocialMediaPosts::class);
        });

        test('process with file inputs', function () {
            $this->component
                ->set('document', $this->document)
                ->set('sourceType', SourceProvider::PDF->value)
                ->set('context', 'any context')
                ->set('tone', Tone::ACADEMIC->value)
                ->set('style', Style::DESCRIPTIVE->value)
                ->set('platforms', [
                    "Linkedin" => false,
                    "Facebook" => true,
                    "Instagram" => false,
                    "Twitter" => false
                ])
                ->set('keyword', '123')
                ->set('wordCountTarget', '150')
                ->set('moreInstructions', 'more instructions')
                ->set('generateImage', true)
                ->set('imgPrompt', 'image prompt')
                ->set('fileInput', UploadedFile::fake()->create('avatar.pdf'))
                ->call('process')
                ->assertHasNoErrors()
                ->assertSet('generating', true);

            $this->document->refresh();
            $this->assertNotNull($this->document->getMeta('source_file_path'));
            $this->assertEmpty($this->document->getMeta('source_urls'));
            $this->assertDatabaseHas('documents', [
                'id' => $this->document->id,
                'meta->context' => 'any context',
                'meta->tone' => Tone::ACADEMIC->value,
                'meta->style' => Style::DESCRIPTIVE->value,
                'meta->source' => SourceProvider::PDF->value,
                'meta->keyword' => '123',
                'meta->target_word_count' => '150',
                'meta->more_instructions' => 'more instructions',
                'meta->generate_img' => true,
                'meta->img_prompt' => 'image prompt',
            ]);

            Bus::assertDispatched(ProcessSocialMediaPosts::class);
        });
    }
)->group('social-media');
