<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <div class="w-1/3 md:w-1/4 m-auto mb-8">
                <img src="/logo.png" class=""/>
            </div>
        </x-slot>

        <div class="flex flex-col mb-6 font-courier">
            <div class="text-3xl font-bold text-zinc-700">Sign-in</div>
            <div>or <a class="underline" href="{{ route('register') }}">create an account</a></div>
        </div>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 font-courier">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-jet-label class='font-courier' for="email" value="{{ __('Email') }}" />
                <x-jet-input class='font-courier' id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label class='font-courier' for="password" value="{{ __('Password') }}" />
                <x-jet-input class='font-courier' id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="flex justify-between py-6">
                <label for="remember_me" class="flex items-center">
                    <x-jet-checkbox class='font-courier' id="remember_me" name="remember" />
                    <span class="font-courier ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="font-courier underline text-xs text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div>
                <x-button type="submit" label="Sign in" red class="font-courier bg-secondary text-lg w-full text-center rounded-lg"/>
            </div>
        </form>
            <div class="mt-6 flex flex-col gap-2">
                <div class="border border-zinc-300 rounded-lg px-3 py-3 flex justify-center">
                    <a href="{{ route('login.google') }}" class="btn btn-primary flex items-center gap-2">
                        <img width="20px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
                            <span class="font-courier">Sign-in with Google</span>
                    </a>
                </div>
                {{-- <div class="border border-zinc-300 rounded-lg px-3 py-3 flex justify-center">
                    <button type="submit" class="btn btn-primary flex items-center gap-2">
                        <img width="20px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
                            <span class="font-">Sign-in with Linkedin</span>
                    </button>
                </div>
                <div class="border border-zinc-300 rounded-lg px-3 py-3 flex justify-center">
                    <button type="submit" class="btn btn-primary flex items-center gap-2">
                        <img width="20px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
                            <span class="font-">Sign-in with Medium</span>
                    </button>
                </div> --}}
            </div>


    </x-jet-authentication-card>
</x-guest-layout>
