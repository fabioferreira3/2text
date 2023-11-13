<?php

namespace App\Models\Traits;

use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\MediaFile;
use App\Repositories\DocumentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait SocialMediaTrait
{
    public $text;
    public $textBlockId;
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
            'toggleImageGenerator',
            'imageSelected',
            'contentBlockUpdated'
        ];
    }

    public function copy()
    {
        $this->emit('addToClipboard', $this->text);
        $this->copied = true;
    }

    public function save()
    {
        $this->saving = true;
        DocumentRepository::updateContentBlock($this->textBlockId, [
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
        $this->emitUp('deleteSocialMediaPost', [
            'document_id' => $this->document->id
        ]);
    }

    public function downloadImage()
    {
        $originalFile = MediaFile::where('file_url', $this->image)->first();
        return Storage::download($originalFile->file_path);
    }

    public function imageSelected($params)
    {
        $mediaFile = MediaFile::findOrFail($params['media_file_id']);
        $this->imageBlock->update(['content' => $mediaFile->id]);
        $this->refreshImage();
        $this->toggleImageGenerator();
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('alerts.image_updated')
        ]);
    }

    public function toggleImageGenerator($defaultImg = null)
    {
        if (!$this->imageBlock) {
            $imageBlock = $this->document->contentBlocks()->save(
                new DocumentContentBlock([
                    'type' => 'image',
                    'content' => ''
                ])
            );
            $this->imageBlock = $imageBlock;
            $this->imageBlockId = $imageBlock ? $imageBlock->id : null;
            $this->imagePrompt = '';
            $this->document->refresh();
        }
        $this->showImageGenerator = !$this->showImageGenerator;
        if ($defaultImg) {
            $this->emitTo('image.image-block-generator-modal', 'setOriginalPreviewImage', [
                'file_url' => $defaultImg
            ]);
        }
    }

    public function refreshImage()
    {
        $imageBlock = $this->imageBlock ?? optional($this->document->contentBlocks)->firstWhere('type', 'media_file_image');
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

    public function contentBlockUpdated($params)
    {
        if ($params['document_content_block_id'] === $this->imageBlockId) {
            $this->refreshImage();
        } elseif ($params['document_content_block_id'] === $this->textBlockId) {
            $this->refreshText();
        }
    }
}
