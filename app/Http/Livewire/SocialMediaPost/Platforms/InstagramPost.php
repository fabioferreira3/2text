<?php

namespace App\Http\Livewire\SocialMediaPost\Platforms;

use App\Models\Document;
use App\Models\Traits\SocialMediaTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class InstagramPost extends Component
{
    use SocialMediaTrait;

    public Document $document;
    public bool $displayHistory = false;
    public string $userId;
    private string $platform;

    public function getListeners()
    {
        return [
            'refresh',
            'showHistoryModal',
            'closeHistoryModal',
            'textBlockUpdated',
            "echo-private:User.$this->userId,.ProcessFinished" => 'finishProcessing',
        ];
    }

    public function mount(Document $document)
    {
        $this->userId = Auth::user()->id;
        $this->document = $document;
        $imageBlock = optional($this->document->contentBlocks)->firstWhere('type', 'image');
        $textBlock = optional($this->document->contentBlocks)->firstWhere('type', 'text');

        $this->image = $imageBlock ? $imageBlock->content : null;
        $this->imageBlockId = $imageBlock ? $imageBlock->id : null;
        $this->text = $textBlock ? Str::of($textBlock->content)->trim('"') : '';
        $this->textBlockId = $textBlock ? $textBlock->id : null;
    }

    public function render()
    {
        return view('livewire.social-media-post.platforms.instagram-post');
    }

    public function showHistoryModal()
    {
        $this->displayHistory = true;
        $this->emit('listDocumentHistory', $this->platform, true);
    }

    public function closeHistoryModal()
    {
        $this->displayHistory = false;
    }
}
