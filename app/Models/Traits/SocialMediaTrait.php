<?php

namespace App\Models\Traits;

use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

trait SocialMediaTrait
{
    public $text;
    public string | null $textBlockId;
    public $image;
    public $imageBlockId;
    public $imagePrompt;
    public $imageBlock;
    public $saving = false;
    public bool $showImageGenerator;
    public bool $copied;

    public function mount(Document $document)
    {
        $this->saving = false;
        $this->userId = Auth::user()->id;
        $this->document = $document;
        $this->showImageGenerator = false;
        $this->refreshImage();
        $this->refreshText();
    }

    public function getListeners()
    {
        return [
            'textBlockUpdated',
            'contentBlockUpdated'
        ];
    }

    public function copy()
    {
        $this->dispatch('addToClipboard', message: $this->text->value);
        $this->copied = true;
    }

    public function save()
    {
        $this->saving = true;
        $this->repo->updateContentBlock($this->textBlockId, [
            'content' => $this->text
        ]);
        $this->saving = false;
    }

    public function textBlockUpdated($params)
    {
        if (isset($params['content'])) {
            $this->text = $params['content'];
            $this->save();
        }
    }

    public function delete()
    {
        $this->dispatch('deleteSocialMediaPost', [
            'document_id' => $this->document->id
        ]);
    }

    public function downloadImage()
    {
        $originalFile = MediaFile::where('file_url', $this->image)->first();
        return Storage::download($originalFile->file_path);
    }

    #[On('imageSelected')]
    public function imageSelected($mediaFileId)
    {
        $this->imageBlock->update(['content' => $mediaFileId]);
        $this->refreshImage();
        $this->toggleImageGenerator();
        $this->dispatch(
            'alert',
            type: 'success',
            message: __('alerts.image_updated')
        );
    }

    public function refreshImage()
    {
        $imageBlock = $this->imageBlock ?? optional($this->document->contentBlocks)
            ->firstWhere('type', 'media_file_image');
        $this->image = $imageBlock ? $imageBlock->getUrl() : null;
        $this->imageBlock = $imageBlock ?? null;
        $this->imageBlockId = $imageBlock ? $imageBlock->id : null;
        $this->imagePrompt = $imageBlock->prompt ?? '';
    }

    public function refreshText()
    {
        $textBlock = optional($this->document->contentBlocks)->firstWhere('type', 'text');
        $this->text = $textBlock ? Str::of($textBlock->content)->trim('"') : '';
        $this->textBlockId = $textBlock ? $textBlock->id : null;
    }

    public function contentBlockUpdated($documentContentBlockId)
    {
        if ($documentContentBlockId === $this->imageBlockId) {
            $this->refreshImage();
        } elseif ($documentContentBlockId === $this->textBlockId) {
            $this->refreshText();
        }
    }
}
