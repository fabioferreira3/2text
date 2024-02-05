<?php

namespace App\Livewire\Common;

use Livewire\Component;

class Header extends Component
{
    public $document = null;
    public $editable = false;
    public $icon;
    public $title = '';
    public $suffix = null;

    protected $listeners = ['titleUpdated' => 'updateTitle'];

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('livewire.common.header');
    }

    public function updateTitle($title)
    {
        $this->title = $title;
    }

    public function updatedTitle($newTitle)
    {
        if ($this->document) {
            $this->document->update(['title' => $newTitle]);
            $this->dispatch(
                'alert',
                type: 'success',
                message: __('common.title_updated')
            );
        }
    }
}
