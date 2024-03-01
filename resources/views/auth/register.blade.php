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
            <h1 class="text-3xl font-bold text-zinc-700">New account</h1>
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
    <div class="hidden md:grid gap-6 md:grid-cols-3 xl:grid-cols-6 px-12 mt-8">
        @include('components.testimonial', [
        'quote' => 'Experior saves a lot of time on creating content.',
        'img' => 'https://experior.ai/_next/image?url=%2Ftestimonials%2Fhreedi.jpeg&w=64&q=75',
        'author' => 'Hreedi'
        ])
        @include('components.testimonial', [
        'quote' => 'Experior makes my job of finding relavant assistance for work or leisure much more fun',
        'img' => 'https://experior.ai/_next/image?url=%2Ftestimonials%2Fkarl.jpeg&w=64&q=75',
        'author' => 'Karl'
        ])
        @include('components.testimonial', [
        'quote' => 'I see this as the go-to tool for all kinds of very useful content related AI tools.',
        'img' => 'https://experior.ai/_next/image?url=%2Ftestimonials%2Fmathew.jpeg&w=64&q=75',
        'author' => 'Matthew'
        ])
        @include('components.testimonial', [
        'quote' => 'It saves a lot of time thinking up ideas for interesting content.',
        'img' => 'https://experior.ai/_next/image?url=%2Ftestimonials%2Fgenevieve.jpeg&w=64&q=75',
        'author' => 'Genevieve'
        ])
        @include('components.testimonial', [
        'quote' => 'It allows me to put my time elsewhere and make engaging posts to help grow my business
        following.',
        'img' => 'https://experior.ai/_next/image?url=%2Ftestimonials%2Fcherie.jpg&w=64&q=75',
        'author' => 'Cherie'
        ])
        @include('components.testimonial', [
        'quote' => 'The quality of blog content generated was impressive.',
        'img' => 'https://experior.ai/_next/image?url=%2Ftestimonials%2Fchristine_t.jpeg&w=64&q=75',
        'author' => 'Christine'
        ])
    </div>

</x-guest-layout>
