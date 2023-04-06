<?php

namespace App\Observers;

use App\Helpers\TextRequestHelper;
use App\Models\TextRequestLog;

class TextRequestLogObserver
{

    /**
     * Handle the TextRequest "creating" event.
     *
     * @param  \App\Models\TextRequest  $textRequest
     * @return void
     */
    public function saving(TextRequestLog $textRequestLog)
    {
        if ($textRequestLog->isDirty('model')) {
            $textRequestLog->costs = TextRequestHelper::calculateModelCosts($textRequestLog->model, [
                'prompt' => $textRequestLog->prompt_token_usage,
                'completion' => $textRequestLog->completion_token_usage,
                'total' => $textRequestLog->total_token_usage
            ]);
        }
    }
}
