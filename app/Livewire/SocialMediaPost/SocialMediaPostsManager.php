<?php

namespace App\Livewire\SocialMediaPost;

use App\Enums\DocumentStatus;
use App\Enums\Language;
use App\Enums\SourceProvider;
use App\Enums\Tone;
use App\Exceptions\CreatingSocialMediaPostException;
use App\Exceptions\InsufficientUnitsException;
use App\Jobs\SocialMedia\ProcessSocialMediaPosts;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Repositories\DocumentRepository;
use App\Rules\CsvFile;
use App\Rules\DocxFile;
use App\Rules\PdfFile;
use App\Traits\UnitCheck;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Talendor\StabilityAI\Enums\StylePreset;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SocialMediaPostsManager extends Component
{
    use WithFileUploads, UnitCheck;

    public Document $document;
    public string $content;
    public $context;
    public $fileInput = null;

    public array $sourceUrls;
    public string $tempSourceUrl;
    public bool $maxSourceUrlsReached;
    public $sourceType;

    public string $imgPrompt;
    public $wordCountTarget;
    public string $language;
    public array $languages;
    public string $keyword;
    public mixed $tone;
    public mixed $style;
    public array $platforms;
    public mixed $moreInstructions;
    public bool $showInstructions;
    public bool $generateImage;
    public bool $showImageGenerator = false;
    public bool $modal;
    public $title;
    public bool $generating;
    public $filePath = null;

    public $selectedImageBlock;
    public $selectedImageBlockId;
    public $imagePrompt;
    public $currentProgress = 0;

    public $posts = [];

    public function rules()
    {
        return [
            'document' => [
                'required'
            ],
            'sourceType' => [
                'required',
                Rule::in(array_map(fn ($value) => $value->value, SourceProvider::cases()))
            ],
            'sourceUrls' => [
                'required_if:sourceType,youtube,website_url',
                'array',
                function ($attribute, $value, $fail) {
                    if (request()->input('sourceType') === 'youtube' && count($value) > 3) {
                        return $fail('The maximum number of Youtube sources is 3.');
                    }
                    if (request()->input('sourceType') === 'website_url' && count($value) > 5) {
                        return $fail('The maximum number of source URLs is 5.');
                    }
                },
            ],
            'sourceUrls.*' => ['url'],
            'imgPrompt' => 'required_if:generateImage,true',
            'platforms' => ['required', 'array', new \App\Rules\ValidPlatforms()],
            'context' => [
                'required_if:sourceType,free_text',
                'nullable',
                'string',
                'max:30000'
            ],
            'keyword' => 'required',
            'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'tone' => 'nullable',
            'style' => 'nullable',
            'wordCountTarget' => 'numeric|min:20|max:400',
            'fileInput' => [
                'required_if:sourceType,docx,pdf,csv',
                'max:51200', // in kilobytes, 50mb = 50 * 1024 = 51200kb
                new DocxFile($this->sourceType),
                new PdfFile($this->sourceType),
                new CsvFile($this->sourceType),
            ]
        ];
    }

    public function messages()
    {
        return [
            'context.required_if' => __('validation.context_required'),
            'sourceUrls.required_if' => __('validation.social_media_sourceurl_required'),
            'sourceUrls.*.url' => __('validation.active_url'),
            'keyword.required' => __('validation.keyword_required'),
            'sourceType.required' => __('validation.source_required'),
            'language.required' => __('validation.language_required'),
            'imgPrompt.required_if' => __('validation.img_prompt_required')
        ];
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'finishedProcess',
            'deleteSocialMediaPost' => 'deleteDocument'
        ];
    }

    public function storeFile()
    {
        $accountId = Auth::check() ? Auth::user()->account_id : 'guest';
        $filename = Str::uuid() . '.' . $this->fileInput->getClientOriginalExtension();
        $this->filePath = "documents/$accountId/" . $filename;
        $this->fileInput->storeAs("documents/$accountId", $filename, 's3');
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->generating = in_array($this->document->status, [
            DocumentStatus::ON_HOLD,
            DocumentStatus::IN_PROGRESS
        ]);
        //$this->generating = true;
        $this->checkDocumentStatus();
        $this->sourceType = $document->getMeta('source') ?? 'free_text';
        $this->context = $document->getContext() ?? '';
        $this->sourceUrls = $document->getMeta('source_urls') ?? [];
        $this->tempSourceUrl = '';
        $this->generateImage = $document->getMeta('generate_img') ?? false;
        $this->imgPrompt = $document->getMeta('img_prompt') ?? '';
        $this->language = $document->language->value ?? 'en';
        $this->languages = Language::getLabels();
        $this->wordCountTarget = $document->getMeta('target_word_count') ?? 50;
        $this->keyword = $document->getMeta('keyword') ?? '';
        $this->tone = $document->getMeta('tone') ?? 'default';
        $this->style = $document->getMeta('style') ?? 'default';
        $this->title = '';
        $this->moreInstructions = $document->getMeta('more_instructions') ?? null;
        $this->platforms = [
            'Linkedin' => false,
            'Facebook' => false,
            'Instagram' => false,
            'Twitter' => false
        ];
        $this->posts = $document->children()->ofMediaPosts()->latest()->get();
        $this->checkMaxSourceUrls();
    }

    public function checkMaxSourceUrls()
    {
        $isMaxReached = false;

        if (($this->sourceType === SourceProvider::YOUTUBE->value && count($this->sourceUrls) >= 3) ||
            ($this->sourceType === SourceProvider::WEBSITE_URL->value && count($this->sourceUrls) >= 5)
        ) {
            $isMaxReached = true;
        }

        $this->maxSourceUrlsReached = $isMaxReached;
    }

    public function addSourceUrl()
    {
        if ($this->sourceType === SourceProvider::YOUTUBE->value) {
            $validator = Validator::make(
                ['url' => $this->tempSourceUrl],
                [
                    'url' => [
                        'required',
                        'url',
                        new \App\Rules\YouTubeUrl()
                    ],
                ]
            );
            $validationMsg = 'This is not a valid Youtube URL.';
        } else {
            $validator = Validator::make(['url' => $this->tempSourceUrl], [
                'url' => 'required|url',
            ]);
            $validationMsg = 'The URL is not valid.';
        }

        if ($validator->fails()) {
            $this->addError('tempSourceUrl', $validationMsg);
            return;
        }

        if (!in_array($this->tempSourceUrl, $this->sourceUrls, true)) {
            $this->sourceUrls[] = $this->tempSourceUrl;
        }

        $this->tempSourceUrl = '';
        $this->checkMaxSourceUrls();
        $this->resetErrorBag('tempSourceUrl');
    }

    public function removeSourceUrl(string $sourceUrl)
    {
        $this->sourceUrls = array_filter($this->sourceUrls, function ($url) use ($sourceUrl) {
            return $url !== $sourceUrl;
        });

        $this->sourceUrls = array_values($this->sourceUrls);
        $this->checkMaxSourceUrls();
    }


    public function checkDocumentStatus()
    {
        if ($this->generating) {
            if (!in_array($this->document->status, [
                DocumentStatus::ON_HOLD,
                DocumentStatus::IN_PROGRESS
            ])) {
                $this->generating = false;
            }
            if (!$this->generating) {
                $this->showInstructions = $this->document->status == DocumentStatus::DRAFT ? true : false;
                $this->dispatch(
                    'alert',
                    type: 'success',
                    message: __('alerts.posts_generated')
                );
                $this->posts = $this->document->children()->ofMediaPosts()->latest()->get();
            }
        } else {
            $this->showInstructions = $this->document->status == DocumentStatus::DRAFT ? true : false;
        }
    }

    public function toggleInstructions()
    {
        $this->showInstructions = !$this->showInstructions;
    }

    #[On('toggleImageGenerator')]
    public function toggleImageGenerator($defaultImg = null)
    {
        if (!$this->selectedImageBlock) {
            $imageBlock = $this->document->contentBlocks()->save(
                new DocumentContentBlock([
                    'type' => 'image',
                    'content' => ''
                ])
            );
            $this->selectedImageBlock = $imageBlock;
            $this->selectedImageBlockId = $imageBlock ? $imageBlock->id : null;
            $this->imagePrompt = '';
            $this->document->refresh();
        }
        $this->showImageGenerator = !$this->showImageGenerator;
        if ($defaultImg) {
            $this->dispatch('image.image-block-generator-modal', 'setOriginalPreviewImage', [
                'file_url' => $defaultImg
            ]);
        }
    }

    public function finishedProcess(array $params)
    {
        $this->dispatch('start-processing-animation');
        if (
            isset($params['parent_document_id']) &&
            $params['parent_document_id'] === $this->document->id &&
            $params['group_finished']
        ) {
            $this->checkDocumentStatus();
        }
    }

    public function updatedSourceType()
    {
        $this->context = '';
        $this->moreInstructions = '';
        $this->sourceUrls = [];
        $this->resetErrorBag('fileInput');
        $this->resetErrorBag('tempSourceUrl');
        $this->resetErrorBag('sourceUrls');
    }

    public function deleteDocument(array $params)
    {
        (new DocumentRepository())->delete($params['document_id']);
        $this->dispatch(
            'alert',
            type: 'success',
            message: __('alerts.post_deleted')
        );
        redirect(request()->header('Referer'));
    }

    public function updatedFileInput($file)
    {
        $this->storeFile($file);
    }

    public function process()
    {
        $this->validate();
        try {
            $this->validateUnitCosts();

            $this->generating = true;
            $this->dispatch(
                'alert',
                type: 'info',
                message: __('alerts.generating_posts')
            );

            $this->document->update([
                'language' => $this->language ?? $this->document->language->value,
                'meta' => [
                    'context' => $this->context ?? null,
                    'tone' => $this->tone ?? Tone::DEFAULT->value,
                    'style' => $this->style ?? null,
                    'source_file_path' => $this->filePath ?? null,
                    'source' => $this->sourceType,
                    'source_urls' => $this->sourceUrls ?? [],
                    'keyword' => $this->keyword ?? null,
                    'target_word_count' => $this->wordCountTarget,
                    'more_instructions' => $this->moreInstructions ?? null,
                    'generate_img' => $this->generateImage,
                    'img_prompt' => $this->generateImage ? $this->imgPrompt ?? StylePreset::DIGITAL_ART->value : null,
                    'user_id' => Auth::check() ? Auth::id() : null
                ]
            ]);

            $platforms = collect($this->platforms)->filter()->toArray();
            ProcessSocialMediaPosts::dispatch($this->document, $platforms);
            $this->moveProgress();
        } catch (InsufficientUnitsException $e) {
            $this->dispatch(
                'alert',
                type: 'error',
                message: __('alerts.insufficient_units')
            );
        } catch (Exception $e) {
            throw new CreatingSocialMediaPostException($e->getMessage());
        }
    }

    public function moveProgress()
    {
        $this->currentProgress++;
        $this->dispatch('progressUpdated', $this->currentProgress);
    }

    public function validateUnitCosts()
    {
        $this->totalCost = 0;
        $platforms = collect($this->platforms)->filter();
        $this->estimateWordsGenerationCost($this->wordCountTarget * count($platforms));
        if ($this->generateImage) {
            $this->estimateImageGenerationCost(count($platforms));
        }
        $this->authorizeTotalCost();
    }

    public function render()
    {
        return view('livewire.social-media-post.posts-manager')->title(__('social_media.title'));
    }
}
