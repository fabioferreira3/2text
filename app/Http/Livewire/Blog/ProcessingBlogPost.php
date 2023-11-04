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
    public $tasksProgress = 7;
    public string $currentThought;
    public $thoughts;

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
        $this->currentThought = __('oraculum.hmmm');
        $this->thoughts = null;
    }

    public function taskFinished(array $params)
    {
        if ($params['document_id'] === $this->document->id) {
            $this->document->refresh();
            $this->checkStatus($this->document);
            $this->defineThought();
            $this->tasksProgress = $params['tasks_progress'] > 100 ? 99 : $params['tasks_progress'];
        }
    }

    public function defineThought()
    {
        $this->thoughts = $this->thoughts ?? $this->document->getMeta('thoughts') ?? null;

        if ($this->thoughts && count($this->thoughts) > 0) {
            $this->currentThought = $this->thoughts[array_rand($this->thoughts)];
        } else {
            $this->currentThought = __('oraculum.where_to_start');
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
            DocumentStatus::FAILED,
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
