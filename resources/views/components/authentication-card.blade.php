<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-main">
    <div>
        {{ $logo }}
    </div>

    <div class="flex w-full justify-center mt-6 gap-8">
        <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
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
