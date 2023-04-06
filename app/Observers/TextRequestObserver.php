<?php

namespace App\Observers;

use App\Models\TextRequest;
use Illuminate\Support\Str;

class TextRequestObserver
{

    /**
     * Handle the TextRequest "creating" event.
     *
     * @param  \App\Models\TextRequest  $textRequest
     * @return void
     */
    public function saving(TextRequest $textRequest)
    {
        if ($textRequest->isDirty('final_text')) {
            $textRequest->word_count = Str::wordCount($textRequest->final_text);
            $textRequest->char_count = strlen($textRequest->final_text);
        }
    }
}
