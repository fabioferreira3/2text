<div>
    <div class="mb-8">
        @include('livewire.common.label', ['title' => $title])
    </div>
    <div class="flex items-center px-8">
        <div id="robot-working" wire:key="robot-working" class="w-1/2"></div>
        <div class="flex flex-col w-1/2 gap-4">
            <div id="blog-writing" wire:key="blog-writing" class="w-2/3 place-self-center"></div>
            <div class="font-bold text-3xl">
                {{ $thought }}
            </div>
        </div>
        @if($tasksProgress > 0)<div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
            <div class="bg-secondary text-base font-medium text-white text-center p-3 leading-none rounded-full"
                style="width: {{ $tasksProgress }}%;">
                {{ $tasksProgress }}%
            </div>
        </div>
        @endif
    </div>
</div>
</div>

@push('scripts')
<script>
    function initAnimations() {
        if (document.getElementById('robot-working')) {
            lottie.loadAnimation({
                container: document.getElementById('robot-working'), // Required
                path: '/animations/robot-working1.json', // Required
                renderer: 'svg', // Required
                loop: true, // Optional
                autoplay: true, // Optional
            });
        }

        if (document.getElementById('blog-writing')) {
            lottie.loadAnimation({
                container: document.getElementById('blog-writing'), // Required
                path: '/animations/blog-writing1.json', // Required
                renderer: 'svg', // Required
                loop: true, // Optional
                autoplay: true, // Optional
            });
        }
    }

    document.addEventListener('DOMContentLoaded', initAnimations);

    Livewire.hook('message.processed', (message, component) => {
        // Reinitialize Lottie animations if the Livewire component has the specific IDs
        if (document.getElementById('robot-working') || document.getElementById('blog-writing')) {
            initAnimations();
        }
    });
</script>
@endpush
