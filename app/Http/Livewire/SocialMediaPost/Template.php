<?php

namespace App\Http\Livewire\SocialMediaPost;

use App\Jobs\SocialMedia\CreateSocialMediaPost;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class Template extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct()
    {
        $this->icon = 'hashtag';
        $this->title = __('templates.social_media_post');
        $this->description = __('templates.create_social_media');
    }

    public function render()
    {
        return view('livewire.common.template');
    }

    public function execute()
    {
        $repo = new DocumentRepository();
        $document = $repo->createSocialMediaPost([
            'source' => null,
            'context' => null,
            'language' => 'en',
            'meta' => [
                'source_url' => null,
                'tone' => null,
                'style' => null,
                'keyword' => null,
                'more_instructions' => null,
                'platforms' => []
            ]
        ]);

        return redirect()->route('social-media-post-view', ['document' => $document]);
    }
}
