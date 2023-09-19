<?php

namespace App\Http\Livewire\SocialMediaPost\Platforms;

use App\Enums\DocumentTaskEnum;
use App\Jobs\DispatchDocumentTasks;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class InstagramPost extends Component
{
    public Document $document;
    public $image;
    public string $text;
    public string $initialContent;
    public bool $copied = false;
    public bool $displayHistory = false;
    public string $userId;
    public bool $isProcessing = false;
    public string $processId;
    private string $platform;
    private $postData;

    public function getListeners()
    {
        return [
            'refresh',
            'showHistoryModal',
            'closeHistoryModal',
            'refreshContent' => 'updateContent',
            "echo-private:User.$this->userId,.ProcessFinished" => 'finish',
        ];
    }

    public function mount(Document $document, $postData)
    {
        $this->postData = $postData;
        $this->userId = Auth::user()->id;
        $this->document = $document;
        $this->setContent();
        $this->initialContent = $this->text;
        $this->processId = '';
    }

    private function setContent()
    {
        $this->image = isset($this->postData['image_url']) ? Str::of($this->postData['image_url']) : null;
        $this->text = Str::of($this->postData['text'])->trim('"');
    }

    public function refresh()
    {
        $this->document->refresh();
        $this->setContent($this->document);
    }

    public function render()
    {
        return view('livewire.social-media-post.platforms.instagram-post');
    }

    public function regenerate()
    {
        $this->isProcessing = true;
        $this->processId = Str::uuid();
        $repo = new DocumentRepository($this->document);
        $repo->createTask(
            DocumentTaskEnum::CREATE_SOCIAL_MEDIA_POST,
            [
                'process_id' => $this->processId,
                'meta' => [
                    'platform' => $this->platform,
                ],
                'order' => 1
            ]
        );
        $repo->createTask(
            DocumentTaskEnum::REGISTER_FINISHED_PROCESS,
            [
                'process_id' => $this->processId,
                'meta' => [],
                'order' => 2
            ]
        );
        DispatchDocumentTasks::dispatch($this->document);
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

    public function save()
    {
        if ($this->content === $this->initialContent) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'info',
                'message' => "No changes needed to be saved"
            ]);
            return;
        }
        try {
            $repo = new DocumentRepository($this->document);
            $repo->updateMeta($this->platform, $this->content);
            $repo->addHistory(['field' => $this->platform, 'content' => $this->content]);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => "$this->platform post updated!"
            ]);
            $this->initialContent = $this->content;
        } catch (Exception $error) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => "There was an error saving!"
            ]);
        }
    }

    public function updateContent($params)
    {
        if ($params['field'] === $this->platform) {
            $this->setContent();
        }
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
