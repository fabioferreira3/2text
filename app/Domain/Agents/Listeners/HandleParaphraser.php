<?php

namespace App\Domain\Agents\Listeners;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Events\ParaphraserCheckout;
use App\Domain\Agents\Events\ThreadMessagesReceived;
use App\Models\Document;
use App\Models\DocumentContentBlock;

class HandleParaphraser
{
    /**
     * Handle the event.
     *
     * @param ThreadMessagesReceived $event
     * @return void
     */
    public function handle(ThreadMessagesReceived $event)
    {
        if (isset($event->metadata['agent']) && $event->metadata['agent'] === Agent::THE_PARAPHRASER->value) {
            $document = Document::findOrFail($event->metadata['document_id']);
            $message = $event->thread->messages()->fromAssistant()->latest()->first();

            $document->contentBlocks()->save(new DocumentContentBlock([
                'type' => 'text',
                'content' => $message->content['text'],
                'prompt' => null,
                'order' => $event->metadata['sentence_order']
            ]));

            ParaphraserCheckout::dispatch($document);
        }
    }
}
