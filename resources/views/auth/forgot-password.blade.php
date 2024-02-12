<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="https://go.experior.ai">
                <div class="h-full w-1/2 md:w-1/4 xl:w-1/5 m-auto mb-8">
                    <img src="/logo.png" class="pt-8 md:pt-0" />
                </div>
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a
            password reset link that will allow you to choose a new one.') }}
        </div>

        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                    autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="px-4 py-2 text-white bg-main hover:bg-main text-center rounded-lg">{{
                    __('Email Password Reset Link') }}</button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
