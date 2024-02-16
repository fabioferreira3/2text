<?php

namespace App\Livewire;

use Livewire\Component;


class Dashboard extends Component
{
    public $title;
    public $tab;
    protected $queryString = ['tab'];

    public function mount()
    {
        if (!$this->tab) {
            $this->title = 'Dashboard';
            $this->tab = 'dashboard';
        } else {
            $this->defineTitle($this->tab);
        }
    }

    public function render()
    {
        return view('livewire.dashboard')->title($this->title);
    }

    public function updatedTab($value)
    {
        $this->defineTitle($value);
        $this->dispatch('titleUpdated', title: $this->title);
    }

    public function defineTitle($tabValue)
    {
        if ($tabValue === 'images') {
            $this->title = __('dashboard.ai_images');
        } elseif ($tabValue === 'audio') {
            $this->title = __('dashboard.my_audios');
        } else {
            $this->title = __('dashboard.dashboard');
        }
    }
}
