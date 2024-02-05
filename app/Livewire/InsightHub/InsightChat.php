<?php

namespace App\Livewire\InsightHub;

use App\Jobs\Oraculum\Ask;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InsightChat extends Component
{
    public $document;
    public bool $isProcessing;
    public $activeThread;
    public $inputMsg;

    protected $rules = [
        'inputMsg' => 'string|required',
    ];

    public function messages()
    {
        return [
            'inputMsg.required' => __('validation.input_msg_required'),
        ];
    }

    public function getListeners()
    {
        $userId = Auth::user()->id;
        return [
            "echo-private:User.$userId,.ChatMessageReceived" => 'receiveMsg',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->activeThread = $document->chatThread;
        $this->isProcessing = false;
        $this->dispatch('scrollInquiryChatToBottom');
    }

    public function submitMsg()
    {
        $this->validate();
        $this->isProcessing = true;
        $iteration = $this->activeThread->iterations()->create([
            'response' => $this->inputMsg,
            'origin' => 'user'
        ]);
        $this->inputMsg = '';
        $this->activeThread->refresh();
        $this->dispatch('scrollInquiryChatToBottom');
        Ask::dispatch($iteration, $this->document->id);
    }

    public function receiveMsg(array $params)
    {
        if ($params['chat_thread_id'] === $this->activeThread->id) {
            $this->isProcessing = false;
            $this->activeThread->refresh();
            $this->dispatch('scrollInquiryChatToBottom');
        }
    }

    public function render()
    {
        return view('livewire.insight-hub.insight-chat');
    }
}
