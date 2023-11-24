<?php

namespace App\Http\Livewire;

use App\Models\ChatThread;
use App\Models\Traits\ChatTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class Chat extends Component
{
    use ChatTrait;

    public $isOpen;
    public $activeThread;
    public bool $processing;

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

    public function mount()
    {
        $this->isOpen = false;
        $this->activeThread = ChatThread::notDocumentRelated()->latest()->firstOrCreate([]);
        if (!$this->activeThread->iterations->count()) {
            $this->createDefaultSysIteration();
        }
        $this->inputMsg = '';
        $this->processing = false;
    }

    public function createThread()
    {
        $this->processing = false;
        if ($this->activeThread->iterations->count() < 2) {
            return;
        }
        $this->activeThread = ChatThread::create();
        $this->createDefaultSysIteration();
    }

    public function createDefaultSysIteration()
    {
        $this->activeThread->iterations()->create([
            'origin' => 'sys',
            'response' => "Hey " . auth()->user()->name . "! How can I help you?",
        ]);
    }

    public function updatedIsOpen()
    {
        $this->dispatchBrowserEvent('scrollToBottom');
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
