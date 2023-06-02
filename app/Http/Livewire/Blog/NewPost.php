<?php

namespace App\Http\Livewire\Blog;

use App\Enums\Language;
use App\Jobs\Blog\CreateBlogPost;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewPost extends Component
{
    use Actions;

    public string $context;
    public string $source_url;
    public string $source;
    public string $language;
    public array $languages;
    public string $keyword;
    public string $tone;
    public string $style;
    public string $targetHeadersCount;
    public string $instructions;
    public bool $modal;

    public function __construct()
    {
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->keyword = '';
        $this->targetHeadersCount = '3';
        $this->tone = '';
        $this->style = '';
        $this->instructions = '<p>Please fill out the following information so I can understand your requirements and write you an unique and high-quality blog post.</p>

        <h3 class="font-bold">Source</h3><p class="text-sm">Provide a source of the context that your blog post should be based on. It could be a YouTube link, an external web page or just free text.</p>

        <h3 class="font-bold">Keyword</h3><p class="text-sm">Provide a keyword that you would like me to use throughout your blog post. This keyword will help me generate a relevant and focused article.</p>

        <h3 class="font-bold">Number of Topics</h3><p class="text-sm">Indicate the number of topics you would like me to cover in your blog post. You may define a minimum of one and a maximum of fifteen topics.</p>

        <h3 class="font-bold">Language</h3><p class="text-sm">Select the language you would like the blog post to be generated in. If you have provided a YouTube link, please ensure that the selected language matches the main language of the video.</p>

        <h3 class="font-bold">Tone</h3><p class="text-sm">Define the tone of your blog post. You may pick from casual, funny, sarcastic, dramatic, academic, and other tones. This will help me write a blog post that is in line with your preference and your audience\'s.</p>';
    }

    public function render()
    {
        return view('livewire.blog.new');
    }

    protected $rules = [
        'source' => 'required|in:free_text,youtube,website_url',
        'source_url' => 'required_if:source,youtube,website_url|url',
        'context' => 'required_if:source,free_text|nullable',
        'keyword' => 'required',
        'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
        'targetHeadersCount' => 'required|numeric|min:1|max:15',
        'tone' => 'nullable'
    ];

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your blog post.',
        'source_url.required_if' => 'You need to provide a link for me to use as context for your blog post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
        'targetHeadersCount.min' => 'The minimum number of subtopics is 1.',
        'targetHeadersCount.max' => 'The maximum number of subtopics is 15.',
        'targetHeadersCount.required' => 'The number of subtopics is a required field.',
    ];

    public function setSourceInfo()
    {
        $this->instructions = "
        <h2 class='font-bold text-lg'>Source</h2>
        <p>Define where I should extract the base context of your blog post. Choose between free text input, youtube link or a website url.</p>
        <h3 class='mt-4 font-bold'>Youtube</h3>
        <p>Enter a youtube link and I'll write a blog post based on the content of the video.</p>
        <h3 class='mt-4 font-bold'>Website URL</h3>
        <p>Enter an external website url to be used as context, like another blog post or page.
        I'll do my best to extract as much information as possible from that page and use it as context for the creation of your blog post.</p>
        <h3 class='mt-4 font-bold'>Free text</h3>
        <p>Just enter any text that you want as context and I'll write a blog post based on the
        information you provide.</p>";
    }

    public function setKeywordInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Keyword</h2> Define a keyword so the AI may use it more often.";
    }

    public function setLanguageInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Language</h2><p>Define the main language of your blog post.</p><p>Take into account that the selected language must be the same language of the context you're using, ie: the language of the youtube video, the web page or free text you provided.</p>";
    }

    public function setSubtopicsInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Subtopics</h2><p>Define the number of subtopics of your blog post. The more subtopics, more content will be generated.<p>
        <h3 class='font-bold text-sm'>Note</h3>
        <p class='text-sm'>As an estimate, 1 subtopic covers around 350 words.</p>";
    }

    public function setStyleInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Style</h2><p>Define the writing style.<p>
        <div class='font-bold'>Descriptive</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Used to depict imagery to create a clear picture in the mind of the reader.</li>
                <li>Employs literary techniques such as similes, metaphors, allegory, etc to engage the audience.</li>
                <li>Poetry; fictional novels or plays; memoirs or first-hand accounts of events</li>
            </ul>
        <div class='font-bold'>Expository</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Used to explain a concept and share information to a broader audience.</li>
                <li>This type is not meant to express opinions.</li>
                <li>How-to articles; textbooks; news stories; business, technical, or scientific writing</li>
            </ul>
        <div class='font-bold'>Narrative</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Share information in the context of a story.</li>
                <li>Usually includes characters, conflicts, and settings.</li>
                <li>Short stories; novels; poetry; historical accounts </li>
            </ul>
        <div class='font-bold'>Persuasive</div>
            <ul class='list-disc px-4 text-sm'>
                <li>Aims to convince the reader of the validity of a certain position or argument.</li>
                <li>Includes the writers’ opinions, and provides justifications and evidence to support their claims.</li>
                <li>Letters of recommendation; cover letters; newspaper articles; argumentative essays for academic papers</li>
            </ul>";
    }

    public function setToneInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Tone</h2><p>Define the tone of the writing.<p>
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
                'style' => $this->style,
                'keyword' => $this->keyword,
            ]
        ]);

        return redirect()->to('/dashboard');
    }
}
