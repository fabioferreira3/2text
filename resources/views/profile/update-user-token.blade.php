<x-jet-form-section submit="updateAccessToken">
    <x-slot name="title">
        {{ __('Access token') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s access token.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Token -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="token_name" value="{{ __('Token') }}" />
            <x-jet-input :disabled="$state['token_id'] !== null" id="token_name" type="text" class="mt-1 block w-full" wire:model.defer="state.token_name" autocomplete="token_name" />
            <x-jet-input-error for="token_name" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        @if(is_null($state['token_id']))
            <x-jet-button wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-jet-button>
        @endif
    </x-slot>
</x-jet-form-section>
