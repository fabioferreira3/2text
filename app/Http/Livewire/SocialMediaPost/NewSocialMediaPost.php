<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Enums\Language;
use App\Enums\Tone;
use App\Jobs\SocialMedia\CreateSocialMediaPost;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewSocialMediaPost extends Component
{
    use Actions;

    public string $context;
    public string $source_url;
    public string $source;
    public string $language;
    public array $languages;
    public string $keyword;
    public string $tone;
    public bool $linkedin;
    public array $platforms;
    public string $instructions;
    public mixed $more_instructions;
    public bool $modal;
    public $title;

    public function mount()
    {
        $this->title = 'New social media post';
    }

    public function __construct()
    {
        $this->source = 'free_text';
        $this->context = '';
        $this->source_url = '';
        $this->language = 'en';
        $this->languages = Language::getLabels();
        $this->keyword = '';
        $this->tone = '';
        $this->more_instructions = null;
        $this->platforms = [
            'Linkedin' => false,
            'Facebook' => false,
            'Instagram' => false,
            'Twitter' => false,
            'TikTok' => false
        ];
        $this->instructions = '<p>Please fill out the following information so I can understand your requirements and write you an unique and high-quality post.</p>

        <h3 class="font-bold">Target platforms</h3><p class="text-sm">Choose for which platforms you would like me to write a post. For each selected platform I will create a different post.</p>

        <h3 class="font-bold">Source</h3><p class="text-sm">Provide a source of the context that your post should be based on. It could be a YouTube link, an external web page or just free text.</p>

        <h3 class="font-bold">Keyword</h3><p class="text-sm">Provide a keyword that you would like me to use throughout your post. This keyword will help me generate a relevant and focused article.</p>

        <h3 class="font-bold">Language</h3><p class="text-sm">Select the language you would like the post to be generated in. If you have provided a YouTube link, please ensure that the selected language matches the main language of the video.</p>

        <h3 class="font-bold">Tone</h3><p class="text-sm">Define the tone of your post. You may pick from casual, funny, sarcastic, dramatic, academic, and other tones. This will help me write a post that is in line with your preference and your audience\'s.</p>';
    }

    public function render()
    {
        return view('livewire.social-media-post.new')->layout('layouts.app', ['title' => $this->title]);
    }

    protected $rules = [
        'source' => 'required|in:free_text,youtube,website_url',
        'source_url' => 'required_if:source,youtube,website_url|url',
        'platforms' => 'required|array',
        'context' => 'required_if:source,free_text|nullable',
        'keyword' => 'required',
        'language' => 'required|in:en,pt,es,fr,de,it,ru,ja,ko,ch,pl,el,ar,tr',
        'tone' => 'nullable'
    ];

    protected $messages = [
        'context.required_if' => 'You need to provide some context for the AI to generate your social media post.',
        'source_url.required_if' => 'You need to provide a link for me to use as context for your social media post.',
        'keyword.required' => 'You need to provide a keyword.',
        'source.required' => 'Source is a required field.',
        'language.required' => 'Language is a required field.',
    ];

    public function setPlatformsInfo()
    {
        $this->instructions = "
        <h2 class='font-bold text-lg'>Target Platforms</h2><p>Choose for which platforms you would like me to write a post. For each selected platform I will create a different post.</p>";
    }

    public function setSourceInfo()
    {
        $this->instructions = "
        <h2 class='font-bold text-lg'>Source</h2>
        <p>Define where I should extract the base context of your post. Choose between free text input, youtube link or a website url.</p>
        <h3 class='mt-4 font-bold'>Youtube</h3>
        <p>Enter a youtube link and I'll write a social media post based on the content of the video.</p>
        <h3 class='mt-4 font-bold'>Website URL</h3>
        <p>Enter an external website url to be used as context, like a blog post or page.
        I'll do my best to extract as much information as possible from that page and use it as context for the creation of your social media post.</p>
        <h3 class='mt-4 font-bold'>Free text</h3>
        <p>Just enter any text that you want as context and I'll write a post based on the
        information you provide.</p>";
    }

    public function setKeywordInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Keyword</h2> Define a keyword so the AI may use it more often.";
    }

    public function setLanguageInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Language</h2><p>Define the main language of your post.</p><p>Take into account that the selected language must be the same language of the context you're using, ie: the language of the youtube video, the web page or free text you provided.</p>";
    }

    public function setToneInfo()
    {
        $this->instructions = "<h2 class='font-bold'>Tone</h2><p>Define the tone/style of the writing.<p>
        <h3 class='font-bold text-sm'>Useful guidelines</h3>
            <ul>
                <li>Take into account your readers.</li>
                <li>Is it a serious topic? Or could be a fun one?</li>
                <li>What is the reaction you expect from your readers?</li>
            </ul>";
    }

    public function process()
    {
        $this->validate();
        CreateSocialMediaPost::dispatch([
            'source' => $this->source,
            'context' => $this->context,
            'language' => $this->language,
            'meta' => [
                'source_url' => $this->source_url,
                'tone' => $this->tone,
                'keyword' => $this->keyword,
                'more_instructions' => $this->more_instructions,
                'platforms' => $this->platforms
            ]
        ]);

        return redirect()->to('/dashboard');
    }
}
