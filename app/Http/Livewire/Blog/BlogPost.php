<?php

namespace App\Http\Livewire\Blog;

use App\Enums\DocumentStatus;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BlogPost extends Component
{
    public Document $document;
    public $title;
    public $showInfo = false;
    public $tasksProgress = '0%';

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.DocumentTaskFinished" => 'taskFinished',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->title = $this->defineTitle();
    }

    public function defineTitle()
    {
        if (in_array($this->document->status, [
            DocumentStatus::DRAFT,
            DocumentStatus::IN_PROGRESS
        ])) {
            return 'Generating...';
        }

        return $this->document->title ?? 'Blog post';
    }

    public function copyPost()
    {
        $content = $this->document->getHtmlContentBlocksAsText();
        $this->emit('addToClipboard', $content);
        $this->dispatchBrowserEvent('alert', [
            'type' => 'info',
            'message' => __('common.copied_to_clipboard')
        ]);
    }

    public function taskFinished(array $params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->tasksProgress = $params['tasks_progress'];
            $this->defineTitle();
        }
    }

    public function render()
    {
        if (in_array($this->document->status, [DocumentStatus::DRAFT, DocumentStatus::IN_PROGRESS])) {
            return view('livewire.blog.blog-post-processing')->layout('layouts.app');
        }

        return view('livewire.blog.blog-post')->layout('layouts.app', ['title' => $this->title]);
    }
}
