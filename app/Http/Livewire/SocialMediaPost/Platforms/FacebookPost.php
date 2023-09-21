<?php

namespace App\Http\Livewire\SocialMediaPost\Platforms;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class FacebookPost extends Component
{
    public Document $document;
    public string $initialContent;
    public bool $copied = false;
    public bool $displayHistory = false;
    public string $userId;
    public bool $isProcessing = false;
    public string $processId;
    private string $platform;
    public $text;
    public $textBlockId;
    public $image;
    public $imageBlockId;

    public function getListeners()
    {
        return [
            'refresh',
            'showHistoryModal',
            'closeHistoryModal',
            "echo-private:User.$this->userId,.ProcessFinished" => 'finish',
        ];
    }

    public function mount(Document $document)
    {
        $this->userId = Auth::user()->id;
        $this->document = $document;
        $this->processId = '';
        $imageBlock = optional($this->document->contentBlocks)->firstWhere('type', 'image');
        $textBlock = optional($this->document->contentBlocks)->firstWhere('type', 'text');

        $this->image = $imageBlock ? $imageBlock->content : null;
        $this->imageBlockId = $imageBlock ? $imageBlock->id : null;
        $this->text = $textBlock ? Str::of($textBlock->content)->trim('"') : null;
        $this->textBlockId = $textBlock ? $textBlock->id : null;
    }

    public function render()
    {
        return view('livewire.social-media-post.platforms.facebook-post');
    }

    public function copy()
    {
        $this->emit('addToClipboard', $this->content);
        $this->copied = true;
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

    public function finish(array $params)
    {
        if (
            $this->document && $params['document_id'] === $this->document->id
            && $params['process_id'] === $this->processId
        ) {
            $this->refresh();
            $this->isProcessing = false;
        }
    }
}
