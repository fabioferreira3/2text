<div>
    <div class="mb-8">
        @include('livewire.common.label', ['title' => $title])
    </div>
    <div class="flex items-start px-8">
        <div id="robot-working" wire:key="robot-working" class="w-1/2"></div>
        <div class="flex flex-col w-1/2">
            <div class="flex w-full h-64">
                <div id="binary-code1" wire:key="binary-code1"></div>
                <div id="binary-code2" wire:key="binary-code2"></div>
                <div id="binary-code3" wire:key="binary-code3"></div>
            </div>
            <div class="font-bold text-3xl italic h-24">
                "{{ $currentThought }}"
            </div>
            <div class="w-full bg-gray-200 rounded-xl dark:bg-gray-700 mt-4">
                <div class="bg-secondary text-lg font-bold text-white text-center px-3 py-5 leading-none rounded-xl" style="width: {{ $currentProgress }}%;">
                    {{ $currentProgress }}%
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    function initAnimation(containerId, animationPath) {
        const container = document.getElementById(containerId);
        // Check if the animation instance already exists
        if (container) {
            // Dispose of the current animation if it exists to avoid memory leaks
            if (container._lottie) {
                container._lottie.destroy();
            }
            // Initialize the new animation
            container._lottie = lottie.loadAnimation({
                container: container,
                path: animationPath,
                renderer: 'svg',
                loop: true,
                autoplay: true,
            });
        }
    }

    function initAnimations() {
        initAnimation('robot-working', '/animations/robot-working1.json');
        initAnimation('binary-code1', '/animations/binary-code.json');
        initAnimation('binary-code2', '/animations/binary-code.json');
        initAnimation('binary-code3', '/animations/binary-code.json');
        // if (document.getElementById('robot-working')) {
        //     lottie.loadAnimation({
        //         container: document.getElementById('robot-working'),
        //         path: '/animations/robot-working1.json',
        //         renderer: 'svg',
        //         loop: true,
        //         autoplay: true,
        //     });
        // }

        // if (document.getElementById('binary-code1')) {
        //     lottie.loadAnimation({
        //         container: document.getElementById('binary-code1'),
        //         path: '/animations/binary-code.json',
        //         renderer: 'svg',
        //         loop: true,
        //         autoplay: true,
        //     });
        // }

        // if (document.getElementById('binary-code2')) {
        //     lottie.loadAnimation({
        //         container: document.getElementById('binary-code2'),
        //         path: '/animations/binary-code.json',
        //         renderer: 'svg',
        //         loop: true,
        //         autoplay: true,
        //     });
        // }

        // if (document.getElementById('binary-code3')) {
        //     lottie.loadAnimation({
        //         container: document.getElementById('binary-code3'),
        //         path: '/animations/binary-code.json',
        //         renderer: 'svg',
        //         loop: true,
        //         autoplay: true,
        //     });
        // }
    }

    document.addEventListener('DOMContentLoaded', initAnimations);

    Livewire.hook('message.processed', (message, component) => {
        // Reinitialize Lottie animations if the Livewire component has the specific IDs
        initAnimations();
    });
</script>
@endpush