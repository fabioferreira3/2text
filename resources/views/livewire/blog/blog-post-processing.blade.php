<div>
    <div class="mb-8">
        @include('livewire.common.label', ['title' => $title])
    </div>
    <div class="flex items-center px-8">
        <div id="robot-working" class="w-1/2"></div>
        <div class="flex flex-col w-1/2 gap-4">
            <div id="chat-bubble" class=" w-full relative flex items-center justify-center">
                <div class="font-bold text-xl absolute z-50 top-36">{{$thought}}</div>
            </div>
            <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
                <div class="bg-secondary text-base font-medium text-white text-center p-3 leading-none rounded-full"
                    style="width: {{ $tasksProgress }}%;" x-data="{ progress: {{ $tasksProgress }} }"
                    x-bind:style="`width: ${progress}%`" x-text="`${progress}%`">
                    {{ $tasksProgress }}%
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lottie.loadAnimation({
          container: document.getElementById('robot-working'),
          renderer: 'svg',
          loop: true,
          autoplay: true,
          path: '/animations/robot-working1.json' // Use Laravel's asset() helper
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        lottie.loadAnimation({
        container: document.getElementById('chat-bubble'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: '/animations/chat-bubble.json' // Use Laravel's asset() helper
        });
    });
</script>
@endpush
