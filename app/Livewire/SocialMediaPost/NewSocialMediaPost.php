<?php

namespace App\Livewire\SocialMediaPost;

use App\Enums\DocumentType;
use App\Repositories\DocumentRepository;
use WireUi\Traits\Actions;
use Livewire\Component;

class NewSocialMediaPost extends Component
{
    use Actions;

    public $document;

    public function mount()
    {
        $repo = new DocumentRepository();
        $this->document = $repo->createSocialMediaDoc([
            'source' => null,
            'context' => null,
            'language' => 'en',
            'meta' => [
                'source_url' => null,
                'tone' => null,
                'style' => null,
                'keyword' => null,
                'more_instructions' => null
            ]
        ], DocumentType::SOCIAL_MEDIA_GROUP);

        return redirect()->route('social-media-view', ['document' => $this->document]);
    }
}
