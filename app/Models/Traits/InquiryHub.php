<?php

namespace App\Models\Traits;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\ChatThread;
use App\Repositories\DocumentRepository;

trait InquiryHub
{
    public function createNewInquiry()
    {
        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::INQUIRY->value,
            'language' => Language::ENGLISH->value
        ]);

        ChatThread::create([
            'document_id' => $document->id
        ]);

        redirect()->route('inquiry-view', ['document' => $document]);
    }
}
