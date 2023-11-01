<?php

namespace App\Http\Livewire\SocialMediaPost;

use Livewire\Component;

class TempNew extends Component
{

    public function render()
    {
        return view('livewire.social-media-post.temp')->layout('layouts.app', ['title' => ' Eita']);
    }

    public function redirectToCreationPage()
    {
        return redirect()->route('new-social-media-post');
    }
}
