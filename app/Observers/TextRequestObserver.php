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
    public function creating(TextRequest $textRequest)
    {
        $textRequest->word_count = Str::wordCount($textRequest->original_text);
    }

    /**
     * Handle the TextRequest "saved" event.
     *
     * @param  \App\Models\TextRequest  $textRequest
     * @return void
     */
    public function saved(TextRequest $textRequest)
    {
        if ($textRequest->isDirty('title')) {
            $textRequest->logs()->create(['type' => 'title', 'content' => $textRequest->title]);
        }

        if ($textRequest->isDirty('paraphrased_text')) {
            $textRequest->logs()->create(['type' => 'paraphrased_text', 'content' => $textRequest->paraphrased_text]);
        }

        if ($textRequest->isDirty('summary')) {
            $textRequest->logs()->create(['type' => 'summary', 'content' => $textRequest->summary]);
        }

        if ($textRequest->isDirty('meta_description')) {
            $textRequest->logs()->create(['type' => 'meta_description', 'content' => $textRequest->meta_description]);
        }
    }
}
