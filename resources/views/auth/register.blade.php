<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="https://experior.ai">
                <div class="h-full w-1/2 md:w-1/4 xl:w-1/5 m-auto mb-8">
                    <img src="/logo.png" class="pt-8 md:pt-0" />
                </div>
            </a>
        </x-slot>

        <div class="flex flex-col mb-6 font-courier">
            <div class="text-3xl font-bold text-zinc-700">New account</div>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-input placeholder="{{ __('Name') }}" id="name"
                    class="font-courier block mt-1 w-full border border-gray-300" type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-input placeholder="{{ __('Email') }}" id="email"
                    class="font-courier block mt-1 w-full border border-gray-300" type="email" name="email"
                    :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-input placeholder="{{ __('Password') }}" id="password"
                    class="font-courier block mt-1 w-full border border-gray-300" type="password" name="password"
                    required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-input placeholder="{{ __('Confirm Password') }}" id="password_confirmation"
                    class="font-courier block mt-1 w-full border border-gray-300" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                <x-label for="terms">
                    <div class="flex items-center">
                        <x-checkbox name="terms" id="terms" required />

                        {{-- <div class="ml-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' => '<a target="_blank" href="https://experior.ai/terms-of-use"
                                class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of
                                Service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'"
                                class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy
                                Policy').'</a>',
                            ]) !!}
                        </div> --}}
                        <div class="ml-2">
                            {!! __('I agree to the :terms_of_service', [
                            'terms_of_service' => '<a target="_blank" href="https://experior.ai/terms-of-use"
                                class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of
                                Service').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
            </div>
            @endif

            <div class="flex items-center justify-end gap-6 mt-8">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
                <button type="submit" class="bg-secondary text-white py-2 px-4 text-center rounded-lg">Register</button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
