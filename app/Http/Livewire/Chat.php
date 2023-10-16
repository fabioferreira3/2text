<?php

namespace App\Http\Livewire;

use App\Models\ChatThread;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class Chat extends Component
{
    public $isOpen;
    public $activeThread;
    public $inputMsg;
    public $processing;

    public function mount()
    {
        $this->isOpen = false;
        $this->activeThread = ChatThread::latest()->firstOrCreate([]);
        $this->inputMsg = '';
        $this->processing = false;
    }

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

    public function createThread()
    {
        $this->processing = false;
        if ($this->activeThread->iterations->count() < 2) {
            return;
        }
        $this->activeThread = ChatThread::create();
    }

    public function submitMsg()
    {
        $this->validate();
        $this->processing = true;
        $iteration = $this->activeThread->iterations()->create([
            'response' => $this->inputMsg,
            'origin' => 'user'
        ]);
        $this->inputMsg = '';
        $this->activeThread->refresh();
        $this->dispatchBrowserEvent('scrollToBottom');
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
