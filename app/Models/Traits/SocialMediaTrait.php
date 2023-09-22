<?php

namespace App\Models\Traits;

use App\Repositories\DocumentRepository;

trait SocialMediaTrait
{
    public $text;
    public $textBlockId;
    public $image;
    public $imageBlockId;
    public bool $saving = false;
    public bool $copied;

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
        (new DocumentRepository())->delete($this->document->id);
    }
}
