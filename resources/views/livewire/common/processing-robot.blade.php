<div class="flex flex-col md:flex-row items-start md:items-center px-8">
    <div class="flex justify-center w-full md:w-1/2">
        <dotlottie-player src="https://lottie.host/cab7c92f-95a8-43a6-9eb9-faa943955313/UFawTK4KUD.json"
            background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>
    </div>
    <div class="flex flex-col gap-4 md:gap-0 w-full md:w-1/2">
        <div class="flex justify-center items-center w-full">
            <dotlottie-player src="https://lottie.host/9bdaa79e-e33f-45b3-8a40-5b7f736e94f8/1czulAGv2v.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
            </dotlottie-player>
            <dotlottie-player src="https://lottie.host/9bdaa79e-e33f-45b3-8a40-5b7f736e94f8/1czulAGv2v.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
            </dotlottie-player>
            <dotlottie-player src="https://lottie.host/9bdaa79e-e33f-45b3-8a40-5b7f736e94f8/1czulAGv2v.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
            </dotlottie-player>
            <dotlottie-player src="https://lottie.host/9bdaa79e-e33f-45b3-8a40-5b7f736e94f8/1czulAGv2v.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
            </dotlottie-player>
        </div>
        @isset($currentThought)
        <div class="font-bold text-3xl italic h-24">
            "{{$currentThought}}"
        </div>
        @endisset
        @isset($currentProgress)
        <div class="w-full bg-gray-200 rounded-xl dark:bg-gray-700 mt-0 md:mt-4">
            <div class="bg-secondary text-lg font-bold text-white text-center px-3 py-5 leading-none rounded-xl"
                style="width: {{$currentProgress}}%;">
                {{$currentProgress}}%
            </div>
        </div>
        @endisset
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function () {
        console.log('caralho');
        window.livewire.on('progressUpdated', function (progress) {
            if (progress < 99) { // Set your desired max progress value
                setTimeout(()=> {
                    @this.call('moveProgress');
                }, 500);
            }
        });
    });
</script>
@endpush
