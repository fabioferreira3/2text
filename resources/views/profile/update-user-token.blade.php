<x-jet-form-section submit="updateAccessToken">
    <x-slot name="title">
        {{ __('profile.access_token') }}
    </x-slot>

    <x-slot name="description">
        {{ __('profile.update_account_access_token') }}
    </x-slot>

    <x-slot name="form">
        <!-- Token -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="token_name" value="{{ __('Token') }}" />
            <x-jet-input @if($state['token_id'] !==null) disabled @endif id="token_name" type="text"
                class="mt-1 block w-full" wire:model="state.token_name" autocomplete="token_name" />
            <x-jet-input-error for="token_name" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('profile.saved') }}
        </x-jet-action-message>

        @if(is_null($state['token_id']))
        <x-jet-button class="bg-secondary" wire:loading.attr="disabled">
            {{ __('profile.save') }}
        </x-jet-button>
        @endif
    </x-slot>
</x-jet-form-section>