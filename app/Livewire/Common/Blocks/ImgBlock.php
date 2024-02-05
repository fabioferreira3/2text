<?php

namespace App\Livewire\Common\Blocks;

use App\Models\DocumentContentBlock;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;


class ImgBlock extends Component
{
    public $contentBlock;
    public $mediaFile;
    public string $content;
    public bool $showImageGenerator = false;
    public bool $processing;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ContentBlockUpdated" => 'onProcessFinished',
            'toggleImageGenerator',
            'imageSelected'
        ];
    }

    public function mount(DocumentContentBlock $contentBlock)
    {
        $this->contentBlock = $contentBlock;
        $this->mediaFile = MediaFile::findOrFail($contentBlock->content);
        $this->content = $contentBlock->content;
    }

    public function downloadImage()
    {
        return Storage::download($this->mediaFile->file_path);
    }

    public function imageSelected(array $params)
    {
        $this->mediaFile = MediaFile::findOrFail($params['media_file_id']);
        $this->contentBlock->update([
            'content' => $this->mediaFile->id
        ]);
        $this->toggleImageGenerator();
    }

    public function toggleImageGenerator()
    {
        $this->showImageGenerator = !$this->showImageGenerator;
    }

    public function previewImage()
    {
        $this->dispatch('openLinkInNewTab', link: $this->mediaFile->file_url);
    }

    public function render()
    {
        return view('livewire.common.blocks.img-block');
    }

    public function onProcessFinished($params)
    {
        if ($params['document_content_block_id'] === $this->contentBlock->id) {
        }
    }
}
