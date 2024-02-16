<?php

namespace App\Models\Traits;

use App\Jobs\Oraculum\Ask;

/**
 * @codeCoverageIgnore
 */
trait ChatTrait
{
    public $inputMsg;

    public function submitMsg($collectionName = 'oraculum')
    {
        $this->validate();
        $this->processing = true;
        $iteration = $this->activeThread->iterations()->create([
            'response' => $this->inputMsg,
            'origin' => 'user'
        ]);
        $this->inputMsg = '';
        $this->activeThread->refresh();
        $this->dispatch('scrollToBottom');
        Ask::dispatch($iteration, [
            'collection_name' => $collectionName
        ]);
    }

    public function receiveMsg(array $params)
    {
        if ($params['chat_thread_id'] === $this->activeThread->id) {
            $this->processing = false;
            $this->activeThread->refresh();
            $this->dispatch('scrollToBottom');
        }
    }
}
