<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <div class="h-full w-1/2 md:w-1/4 xl:w-1/5 m-auto mb-8">
                <img src="/logo.png" class="pt-8 md:pt-0" />
            </div>
        </x-slot>

        <div class="flex flex-col mb-6">
            <div class="text-3xl font-bold text-zinc-700 font-avenir">Sign-in</div>
            <div>or <a class="underline" href="{{ route('register') }}">create an account</a></div>
        </div>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email">{{ __('Email') }}</label>
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <label for="email">{{ __('Password') }}</label>
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="flex justify-between py-6">
                <label for="remember_me" class="flex items-center">
                    <x-jet-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif
            </div>

            <div>
                <x-button type="submit" label="Sign in" class="text-white font-bold bg-primary hover:bg-secondary text-xl xl:text-lg w-full text-center rounded-lg" />
            </div>
        </form>
        <div class="mt-6 flex flex-col gap-2">
            <a href="{{ route('login.google') }}" class="border border-zinc-300 rounded-lg px-3 py-3 btn btn-primary flex items-center justify-center gap-2 w-full">
                <img width="20px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
                <span class="text-center">Sign-in with Google</span>
            </a>
        </div>


    </x-jet-authentication-card>
</x-guest-layout>
