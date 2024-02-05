<?php

namespace App\Livewire\Profile;

use App\Helpers\SupportHelper;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Timezone extends Component
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
        $this->state = ['timezone' => Auth::user()->timezone];
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return void
     */
    public function updateTimezone(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        Validator::make($this->state, [
            'timezone' => ['required', 'string', Rule::in(array_column(SupportHelper::getTimezones(), 'value'))],
        ])->validateWithBag('updateTimezone');

        Auth::user()->update(['timezone' => $this->state['timezone']]);

        $this->dispatch('saved');
        $this->dispatch('refresh-navigation-menu');
        $this->dispatch(
            'alert',
            type: 'success',
            message: __('alerts.timezone_updated')
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
        return view('profile.timezone');
    }
}
