<?php

namespace App\Livewire\InsightHub;

use App\Models\Traits\InsightHub;
use WireUi\Traits\Actions;
use Livewire\Component;

class Dashboard extends Component
{
    use InsightHub, Actions;

    protected $listeners = ['invokeNew' => 'new'];

    public function render()
    {
        return view('livewire.insight-hub.dashboard')
            ->title(__('insight-hub.insight_hub'));
    }

    public function new()
    {
        $this->createNewInsight();
    }
}
