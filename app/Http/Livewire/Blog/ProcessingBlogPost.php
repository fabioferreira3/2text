<?php

namespace App\Http\Livewire\Blog;

use App\Enums\DocumentStatus;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProcessingBlogPost extends Component
{
    public Document $document;
    public $title;
    public $tasksProgress = '0%';
    public string $thought;

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.DocumentTaskFinished" => 'taskFinished',
        ];
    }

    public function mount(Document $document)
    {
        $this->checkStatus($document);

        $this->document = $document;
        $this->title = __('oraculum.oraculum_is_working');
        $this->thought = __('oraculum.hmmm');
    }

    public function taskFinished(array $params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->tasksProgress = $params['tasks_progress'];
            $this->thought = $params['thought'];
            $this->document->refresh();
            $this->checkStatus($this->document);
        }
    }

    protected function checkStatus(Document $document)
    {
        if ($document->status === DocumentStatus::FINISHED) {
            redirect()->route('blog-post-view', [
                'document' => $this->document
            ]);
        } elseif (!in_array($document->status, [
            DocumentStatus::DRAFT,
            DocumentStatus::IN_PROGRESS
        ])) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.blog.blog-post-processing')->layout('layouts.app');
    }
}
