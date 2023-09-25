<?php

namespace App\Models\Traits;

use App\Models\Document;
use App\Models\DocumentContentBlock;
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
    public $imageFileName;
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
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ProcessFinished" => 'finishedProcess',
            'textBlockUpdated',
            'toggleImageGenerator',
            'imageSelected'
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
        return Storage::download($this->imageFileName);
    }

    public function imageSelected($params)
    {
        $this->imageBlock->update(['content' => $params['file_name']]);
        $this->refreshImage();
        $this->toggleImageGenerator();
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => "Image updated successfully!"
        ]);
    }

    public function toggleImageGenerator()
    {
        if (!$this->imageBlock) {
            $this->document->contentBlocks()->save(
                new DocumentContentBlock([
                    'type' => 'image',
                    'content' => ''
                ])
            );
            $this->document->refresh();
            $this->refreshImage();
        }
        $this->showImageGenerator = !$this->showImageGenerator;
    }

    public function finishedProcess(array $params)
    {
        if (isset($params['document_id']) && $params['document_id'] === $this->document->id) {
            $this->refreshImage();
            $this->refreshText();
        }
    }

    public function refreshImage()
    {
        $imageBlock = $this->imageBlock ?? optional($this->document->contentBlocks)->firstWhere('type', 'image');
        $this->imageFileName = $imageBlock ? $imageBlock->content : null;
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
}
