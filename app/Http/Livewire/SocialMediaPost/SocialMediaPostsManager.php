<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Enums\DocumentStatus;
use App\Enums\Language;
use App\Enums\Tone;
use App\Jobs\SocialMedia\ProcessSocialMediaPosts;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Talendor\StabilityAI\Enums\StylePreset;

class SocialMediaPostsManager extends Component
{
    public Document $document;
    public string $content;
    public bool $displayHistory = false;
    public string $context;
    public string $sourceUrl;
    public string $source;
    public string $imgPrompt;
    public $imgStyle;
    public string $language;
    public array $languages;
    public array $stylePresets;
    public string $keyword;
    public mixed $tone;
    public mixed $style;
    public bool $linkedin;
    public array $platforms;
    public mixed $moreInstructions;
    public bool $showInstructions;
    public $selectedStylePreset;
    public bool $generateImage;
    public bool $modal;
    public $title;
    public bool $generating;

    public function rules()
    {
        return [
            'source' => 'required|in:free_text,youtube,website_url',
            'sourceUrl' => 'required_if:source,youtube,website_url|url',
            'imgPrompt' => 'required_if:generateImage,true',
            'imgStyle' => 'required_if:generateImage,true',
            'platforms' => ['required', 'array', new \App\Rules\ValidPlatforms()],
            'context' => 'required_if:source,free_text|nullable',
            'keyword' => 'required',
            'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
            'tone' => 'nullable',
            'style' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'context.required_if' => __('validation.context_required'),
            'sourceUrl.required_if' => __('validation.social_media_sourceurl_required'),
            'keyword.required' => __('validation.keyword_required'),
            'source.required' => __('validation.source_required'),
            'language.required' => __('validation.language_required'),
            'imgPrompt.required_if' => __('validation.img_prompt_required'),
            'imgStyle.required_if' => __('validation.img_style_required')
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

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->generating = false;
        $this->checkDocumentStatus();
        $this->source = $document->getMeta('source') ?? 'free_text';
        $this->context = $document->getContext() ?? '';
        $this->sourceUrl = $document->getMeta('source_url') ?? '';
        $this->generateImage = $document->getMeta('generate_img') ?? false;
        $this->imgPrompt = $document->getMeta('img_prompt') ?? '';
        $this->imgStyle = $document->getMeta('img_style') ?? null;
        $this->language = $document->language->value ?? 'en';
        $this->languages = Language::getLabels();
        $this->stylePresets = StylePreset::getMappedValues();
        $this->keyword = $document->getMeta('keyword') ?? '';
        $this->tone = $document->getMeta('tone') ?? 'default';
        $this->style = $document->getMeta('style') ?? 'default';
        $this->moreInstructions = $document->getMeta('more_instructions') ?? null;
        $this->platforms = [
            'Linkedin' => false,
            'Facebook' => false,
            'Instagram' => false,
            'Twitter' => false
        ];
    }

    public function checkDocumentStatus()
    {
        $this->showInstructions = $this->document->status == DocumentStatus::DRAFT ? true : false;
        if ($this->generating) {
            $this->generating = in_array($this->document->status, [
                DocumentStatus::ON_HOLD,
                DocumentStatus::IN_PROGRESS
            ]);
            if (!$this->generating) {
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'success',
                    'message' => __('alerts.posts_generated')
                ]);
            }
        }
    }

    public function updatedImgStyle($newValue)
    {
        $this->selectedStylePreset = $this->selectStylePreset($newValue);
    }

    public function selectStylePreset($style)
    {
        $found = array_values(array_filter($this->stylePresets, function ($item) use ($style) {
            return $item["value"] === $style;
        }));

        return $found[0] ?? null;
    }

    public function toggleInstructions()
    {
        $this->showInstructions = !$this->showInstructions;
    }

    public function render()
    {
        return view('livewire.social-media-post.posts-manager');
    }

    public function process()
    {
        $this->validate();
        $this->generating = true;
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => __('alerts.generating_posts')
        ]);
        $this->document->update([
            'meta' => [
                'context' => $this->context ?? null,
                'tone' => $this->tone ?? Tone::CASUAL->value,
                'style' => $this->style ?? null,
                'source' => $this->source,
                'source_url' => $this->sourceUrl ?? null,
                'keyword' => $this->keyword ?? null,
                'more_instructions' => $this->moreInstructions ?? null,
                'generate_img' => $this->generateImage,
                'img_prompt' => $this->generateImage ? $this->imgPrompt ?? StylePreset::DIGITAL_ART->value : null,
                'img_style' => $this->generateImage ? $this->imgStyle : null,
                'user_id' => Auth::check() ? Auth::id() : null
            ]
        ]);

        ProcessSocialMediaPosts::dispatch($this->document, $this->platforms);
    }

    public function finishedProcess(array $params)
    {
        if (isset($params['parent_document_id']) && $params['parent_document_id'] === $this->document->id) {
            $this->document->refresh();
            $this->checkDocumentStatus();
        }
    }

    public function updatedSource()
    {
        $this->context = '';
        $this->moreInstructions = '';
    }

    public function deleteDocument(array $params)
    {
        (new DocumentRepository())->delete($params['document_id']);
        $this->document->refresh();
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => (__('alerts.post_deleted'))
        ]);
    }
}
