<x-form-section submit="updateTimezone">
    <x-slot name="title">
        {{ __('profile.timezone') }}
    </x-slot>

    <x-slot name="description">
        {{ __('profile.update_timezone') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="timezone" value="{{ __('profile.timezone') }}" />
            <select id="timezone" name="timezone" wire:model="state.timezone"
                class="w-full p-3 rounded-lg border border-zinc-200">
                @foreach(App\Helpers\SupportHelper::getTimezones() as $timezone)
                <option value={{$timezone['value']}}>{{$timezone['label']}}</option>
                @endforeach
            </select>
            <x-input-error for="timezone" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="mr-3" on="saved">
            {{ __('profile.saved') }}
        </x-action-message>

        <x-button class="bg-secondary text-white" wire:loading.attr="disabled">
            {{ __('profile.save') }}
        </x-button>

    </x-slot>
</x-form-section>
