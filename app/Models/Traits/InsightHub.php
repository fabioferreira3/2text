<?php

namespace App\Models\Traits;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\ChatThread;
use App\Repositories\DocumentRepository;

trait InsightHub
{
    public function createNewInsight()
    {
        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::INQUIRY->value,
            'language' => Language::ENGLISH->value
        ]);

        $thread = ChatThread::create([
            'document_id' => $document->id
        ]);
        $thread->iterations()->create([
            'origin' => 'sys',
            'response' => "Hi " . auth()->user()->name . "! I just read your source. What would you like to know about it?",
        ]);

        redirect()->route('insight-view', ['document' => $document]);
    }
}
