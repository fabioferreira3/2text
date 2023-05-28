<?php

namespace App\Http\Livewire\Profile;

use App\Models\AccessToken;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateUserToken extends Component
{
    use WithFileUploads;

    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount()
    {
        $this->state = Auth::user()->withoutRelations()->toArray();
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return void
     */
    public function updateAccessToken(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        Validator::make($this->state, [
            'token_name' => ['required', 'string', Rule::exists('access_tokens', 'name')->where(function ($query) {
                return $query->whereNull('used_at');
            })],
        ])->validateWithBag('updateAccessToken');

        $token = AccessToken::where('name', $this->state['token_name'])->first();

        Auth::user()->update(['token_id' => $token->id]);
        $token->update(['used_at' => now()]);

        $this->emit('saved');

        $this->emit('refresh-navigation-menu');

        session()->flash('access_granted', 'Access granted! Enjoy our AI tool!');

        return redirect()->to('/dashboard');
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        return Auth::user();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('profile.update-user-token');
    }
}
