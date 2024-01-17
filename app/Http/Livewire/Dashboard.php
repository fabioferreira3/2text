<?php

namespace App\Http\Livewire;

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
        return view('livewire.dashboard')->layout('layouts.app', ['title' => $this->title]);
    }

    public function updatedTab($value)
    {
        $this->defineTitle($value);
        $this->emit('titleUpdated', $this->title);
    }

    private function defineTitle($tabValue)
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
