<div>
    <div class="md:mb-8">
        @include('livewire.common.label', ['title' => $title])
    </div>
    <div class="flex flex-col md:flex-row items-start px-8">
        <div id="robot-working" wire:key="robot-working" class="w-full md:w-1/2"></div>
        <div class="flex flex-col gap-4 md:gap-0 w-full md:w-1/2">
            <div class="flex w-full md:h-64">
                <div id="binary-code1" wire:key="binary-code1"></div>
                <div id="binary-code2" wire:key="binary-code2"></div>
                <div id="binary-code3" wire:key="binary-code3"></div>
            </div>
            <div class="font-bold text-3xl italic h-24">
                "{{ $currentThought }}"
            </div>
            <div class="w-full bg-gray-200 rounded-xl dark:bg-gray-700 mt-4">
                <div class="bg-secondary text-lg font-bold text-white text-center px-3 py-5 leading-none rounded-xl"
                    style="width: {{ $currentProgress }}%;">
                    {{ $currentProgress }}%
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
    }

    document.addEventListener('DOMContentLoaded', initAnimations);

    document.addEventListener('livewire:init', function() {
        window.livewire.on('taskFinished', () => {
            initAnimations();
        });
    })
</script>
@endpush
