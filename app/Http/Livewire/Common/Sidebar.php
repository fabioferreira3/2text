<?php

namespace App\Http\Livewire\Common;

use Livewire\Component;


class Sidebar extends Component
{

    public $active = 'dashboard';

    public function navigate($page)
    {
        $this->active = $page;

        switch ($page) {
            case 'dashboard':
                return redirect()->route('dashboard');
            case 'templates':
                return redirect()->route('templates');
        }
    }

    public function render()
    {
        return view('livewire.common.sidebar');
    }
}
