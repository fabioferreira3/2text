<div>
    <div class="mb-8">
        @include('livewire.common.label', ['title' => $title])
    </div>
    <div class="flex items-center px-8">
        <div id="robot-working" wire:key="robot-working" class="w-1/2"></div>
        <div class="flex flex-col w-1/2 gap-4">
            <div id="binary-code" wire:key="binary-code" class="w-1/3 place-self-center"></div>
            <div class="font-bold text-3xl">
                {{ $currentThought }}
            </div>
            <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
                <div class="bg-secondary text-base font-medium text-white text-center p-3 leading-none rounded-full"
                    style="width: {{ $tasksProgress }}%;">
                    {{ $tasksProgress }}%
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    function initAnimations() {
        if (document.getElementById('robot-working')) {
            lottie.loadAnimation({
                container: document.getElementById('robot-working'),
                path: '/animations/robot-working1.json',
                renderer: 'svg',
                loop: true,
                autoplay: true,
            });
        }

        if (document.getElementById('binary-code')) {
            lottie.loadAnimation({
                container: document.getElementById('binary-code'),
                path: '/animations/binary-code.json',
                renderer: 'svg',
                loop: true,
                autoplay: true,
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
