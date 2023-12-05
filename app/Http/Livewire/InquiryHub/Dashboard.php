<?php

namespace App\Http\Livewire\InquiryHub;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\ChatThread;
use App\Models\Traits\InquiryHub;
use App\Repositories\DocumentRepository;
use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use InquiryHub, Actions;

    public $document;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.inquiry-hub.dashboard')
            ->layout('layouts.app', ['title' => __('inquiry-hub.inquiry_hub')]);
    }

    public function new()
    {
        $this->createNewInquiry();
    }
}
