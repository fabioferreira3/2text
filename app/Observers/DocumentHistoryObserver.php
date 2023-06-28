<?php

namespace App\Observers;

use App\Helpers\DocumentHelper;
use App\Models\DocumentHistory;

class DocumentHistoryObserver
{

    /**
     * Handle the DocumentHistory "saving" event.
     *
     * @param  \App\Models\DocumentHistory  $documentHistory
     * @return void
     */
    public function saving(DocumentHistory $documentHistory)
    {
        if ($documentHistory->isDirty('model')) {
            $documentHistory->cost = DocumentHelper::calculateModelCosts($documentHistory->model, [
                'prompt' => $documentHistory->prompt_token_usage,
                'completion' => $documentHistory->completion_token_usage,
                'audio_length' => $documentHistory->audio_length,
                'char_count' => $documentHistory->char_count,
                'total' => $documentHistory->total_token_usage
            ]);
        }
    }
}
