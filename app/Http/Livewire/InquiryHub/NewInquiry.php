<?php

namespace App\Http\Livewire\InquiryHub;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Repositories\DocumentRepository;
use Livewire\Component;

class NewInquiry extends Component
{
    public function createNewInquiry()
    {
        $document = DocumentRepository::createGeneric([
            'type' => DocumentType::INQUIRY->value,
            'language' => Language::ENGLISH->value
        ]);

        redirect()->route('inquiry-view', ['document' => $document]);
    }

    public function render()
    {
        return view('livewire.inquiry-hub.new');
    }
}
