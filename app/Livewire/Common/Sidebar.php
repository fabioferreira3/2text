<?php

namespace App\Livewire\Common;

use Livewire\Component;


class Sidebar extends Component
{

    public $active = 'dashboard';

    public function navigate($page)
    {
        $this->active = $page;

        switch ($page) {
            case 'dashboard':
                return redirect()->route('home');
            case 'templates':
                return redirect()->route('tools');
        }
    }

    public function render()
    {
        return view('livewire.common.sidebar');
    }
}
