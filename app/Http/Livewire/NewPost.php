<?php

namespace App\Http\Livewire;

use App\Enums\DocumentType;
use App\Jobs\Blog\CreateBlogPost;
use Illuminate\Validation\Rule;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewPost extends Component
{
    use Actions;

    public string $context;
    public string $source_url;
    public string $source;
    public string $language;
    public string $keyword;
    public string $tone;
    public string $targetHeadersCount;
    public string $instructions;
    public bool $modal;

    public function __construct()
    {
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->keyword = '';
        $this->targetHeadersCount = '3';
        $this->tone = '';
        $this->instructions = '';
    }

    public function render()
    {
        return view('livewire.blog.new');
    }

    protected $rules = [
        'source' => 'required|in:free_text,youtube,video',
        'context' => 'required_if:source,free_text|nullable',
        'keyword' => 'required',
        'language' => 'required|in:en,pt',
        'targetHeadersCount' => 'required|numeric|min:1|max:15',
        'tone' => 'nullable'
    ];

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your blog post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
        'targetHeadersCount.min' => 'The minimum number of subtopics is 1.',
        'targetHeadersCount.max' => 'The maximum number of subtopics is 15.',
        'targetHeadersCount.required' => 'The number of subtopics is a required field.',
    ];

    public function setSourceInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Source</h2> Choose between free text input, a youtube link or a video file to be the source of knowledge of your blog post.";
    }

    public function setKeywordInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Keyword</h2> Define a keyword so the AI may use it more often.";
    }

    public function setLanguageInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Language</h2><p>Define the main language of your blog post.</p><p>For example, if it's a youtube video, then it should be the language of the video. If it's based on free text, then it's the language used in the 'context' field.<p>";
    }

    public function setSubtopicsInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Subtopics</h2><p>Define the number of subtopics of your blog post.<p><p>The more subtopics, more content will be generated.<p>
        <h3 class='font-bold text-sm'>Note</h3>
        <p class='text-sm'>Roughly speaking, 1 subtopic covers 350 words.</p>";
    }

    public function setToneInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Tone</h2><p>Define the tone/style of the writing.<p>
        <h3 class='font-bold text-sm'>Useful guidelines</h3>
            <ul>
                <li>Take into account your readers.</li>
                <li>Is it a serious topic? Or could be a fun one?</li>
                <li>Are you telling a history? Of what genre?</li>
                <li>What is the reaction you expect from your readers?</li>
            </ul>";
    }

    public function process()
    {
        $this->validate();
        CreateBlogPost::dispatch([
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->source_url,
                'target_headers_count' => $this->targetHeadersCount,
                'tone' => $this->tone,
                'keyword' => $this->keyword,
            ],
            'type' => DocumentType::BLOG_POST->value,
        ]);

        return redirect()->to('/dashboard');
    }
}
