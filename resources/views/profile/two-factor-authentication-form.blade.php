<x-jet-action-section>
    <x-slot name="title">
        {{ __('profile.two_factor_authentication') }}
    </x-slot>

    <x-slot name="description">
        {{ __('profile.add_additional_security') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="text-lg font-medium text-gray-900">
            @if ($this->enabled)
            @if ($showingConfirmation)
            {{ __('profile.finish_enable_2fa') }}
            @else
            {{ __('profile.you_have_enabled_2fa') }}
            @endif
            @else
            {{ __('profile.you_have_not_enable_2fa') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                {{ __('profile.when_2fa_enabled') }}
            </p>
        </div>

        @if ($this->enabled)
        @if ($showingQrCode)
        <div class="mt-4 max-w-xl text-sm text-gray-600">
            <p class="font-semibold">
                @if ($showingConfirmation)
                {{ __('profile.scan_qr_code') }}
                @else
                {{ __('profile.scan_qr_code_2fa') }}
                @endif
            </p>
        </div>

        <div class="mt-4">
            {!! $this->user->twoFactorQrCodeSvg() !!}
        </div>

        <div class="mt-4 max-w-xl text-sm text-gray-600">
            <p class="font-semibold">
                {{ __('Setup Key') }}: {{ decrypt($this->user->two_factor_secret) }}
            </p>
        </div>

        @if ($showingConfirmation)
        <div class="mt-4">
            <x-jet-label for="code" value="{{ __('Code') }}" />

            <x-jet-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code" wire:model.defer="code" wire:keydown.enter="confirmTwoFactorAuthentication" />

            <x-jet-input-error for="code" class="mt-2" />
        </div>
        @endif
        @endif

        @if ($showingRecoveryCodes)
        <div class="mt-4 max-w-xl text-sm text-gray-600">
            <p class="font-semibold">
                {{ __('profile.store_recovery_codes') }}
            </p>
        </div>

        <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg">
            @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
            <div>{{ $code }}</div>
            @endforeach
        </div>
        @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
            <x-jet-confirms-password wire:then="enableTwoFactorAuthentication">
                <x-jet-button class="bg-primary" type="button" wire:loading.attr="disabled">
                    {{ __('profile.enable') }}
                </x-jet-button>
            </x-jet-confirms-password>
            @else
            @if ($showingRecoveryCodes)
            <x-jet-confirms-password wire:then="regenerateRecoveryCodes">
                <x-jet-secondary-button class="mr-3">
                    {{ __('profile.regenerate_recovery_codes') }}
                </x-jet-secondary-button>
            </x-jet-confirms-password>
            @elseif ($showingConfirmation)
            <x-jet-confirms-password wire:then="confirmTwoFactorAuthentication">
                <x-jet-button type="button" class="mr-3" wire:loading.attr="disabled">
                    {{ __('profile.confirm') }}
                </x-jet-button>
            </x-jet-confirms-password>
            @else
            <x-jet-confirms-password wire:then="showRecoveryCodes">
                <x-jet-secondary-button class="mr-3">
                    {{ __('profile.show_recovery_codes') }}
                </x-jet-secondary-button>
            </x-jet-confirms-password>
            @endif

            @if ($showingConfirmation)
            <x-jet-confirms-password wire:then="disableTwoFactorAuthentication">
                <x-jet-secondary-button wire:loading.attr="disabled">
                    {{ __('profile.cancel') }}
                </x-jet-secondary-button>
            </x-jet-confirms-password>
            @else
            <x-jet-confirms-password wire:then="disableTwoFactorAuthentication">
                <x-jet-danger-button wire:loading.attr="disabled">
                    {{ __('profile.disable') }}
                </x-jet-danger-button>
            </x-jet-confirms-password>
            @endif

            @endif
        </div>
    </x-slot>
</x-jet-action-section>
