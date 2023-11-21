<?php

namespace App\Http\Livewire\InquiryHub;

use App\Models\Traits\InquiryHub;
use Livewire\Component;

class NewInquiry extends Component
{
    use InquiryHub;

    public function render()
    {
        return view('livewire.inquiry-hub.new');
    }
}
