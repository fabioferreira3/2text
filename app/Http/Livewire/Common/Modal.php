<?php

namespace App\Http\Livewire\Common;

use Illuminate\View\Component;

class Modal extends Component
{
    public $sizeConstraints;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($size = 'default')
    {
        switch ($size) {
            case 'small':
                $this->sizeConstraints = "md:w-1/4";
                break;
            default:
                break;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return 'livewire.common.modal';
    }
}
