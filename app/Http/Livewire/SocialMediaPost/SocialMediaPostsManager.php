<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Enums\DocumentStatus;
use App\Enums\Language;
use App\Enums\Tone;
use App\Jobs\SocialMedia\ProcessSocialMediaPosts;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SocialMediaPostsManager extends Component
{
    public Document $document;
    public string $content;
    public bool $displayHistory = false;
    public string $context;
    public string $sourceUrl;
    public string $source;
    public string $language;
    public array $languages;
    public string $keyword;
    public mixed $tone;
    public mixed $style;
    public bool $linkedin;
    public array $platforms;
    public mixed $moreInstructions;
    public bool $showInstructions;
    public bool $generateImage;
    public bool $modal;
    public $title;
    public bool $generating;

    protected $rules = [
        'source' => 'required|in:free_text,youtube,website_url',
        'sourceUrl' => 'required_if:source,youtube,website_url|url',
        'platforms' => 'required|array',
        'context' => 'required_if:source,free_text|nullable',
        'keyword' => 'required',
        'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
        'tone' => 'nullable',
        'style' => 'nullable'
    ];

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your social media post.',
        'sourceUrl.required_if' => 'You need to provide a link for me to use as context for your social media post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
    ];

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'finishedProcess',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->checkDocumentStatus();
        $this->showInstructions = $document->status == DocumentStatus::DRAFT ? true : false;
        $this->source = $document->getMeta('source') ?? 'free_text';
        $this->context = $document->getMeta('context') ?? '';
        $this->sourceUrl = $document->getMeta('source_url') ?? '';
        $this->language = $document->language->value ?? 'en';
        $this->languages = Language::getLabels();
        $this->generateImage = false;
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
        $this->generating = in_array($this->document->status, [DocumentStatus::ON_HOLD, DocumentStatus::IN_PROGRESS]);
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
                'default_img_prompt' => '',
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
}
