<?php

namespace App\Livewire\Profile;

use App\Enums\Language;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Languages extends Component
{
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
        $this->state = ['language' => Auth::user()->account->language];
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return void
     */
    public function updateLanguage(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        Validator::make($this->state, [
            'language' => ['required', 'string', Rule::in(array_column(Language::cases(), 'value'))],
        ])->validateWithBag('updateLanguage');

        $account = Auth::user()->account;
        $account->update(['settings' => [...$account->settings, 'language' => $this->state['language']]]);

        $this->dispatch('saved');
        $this->dispatch('refresh-navigation-menu');
        $this->dispatch(
            'alert',
            type: 'success',
            message: __('alerts.language_updated')
        );
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
        return view('profile.languages');
    }
}
