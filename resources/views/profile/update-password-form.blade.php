<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('profile.update_password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('profile.ensure_account_random_password') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-input id="current_password" placeholder="{{ __('profile.current_password') }}" type="password"
                class="mt-1 block w-full border border-gray-200" wire:model="state.current_password"
                autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-input id="password" placeholder="{{ __('profile.new_password') }}" type="password"
                class="mt-1 block w-full border border-gray-200" wire:model="state.password"
                autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-input id="password_confirmation" placeholder="{{ __('profile.confirm_password') }}" type="password"
                class="mt-1 block w-full border border-gray-200" wire:model="state.password_confirmation"
                autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="mr-3" on="saved">
            {{ __('profile.saved') }}
        </x-action-message>

        <x-button class="bg-secondary text-white">
            {{ __('profile.save') }}
        </x-button>
    </x-slot>
</x-form-section>
