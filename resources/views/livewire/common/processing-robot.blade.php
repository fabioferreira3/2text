<div class="flex flex-col md:flex-row items-start md:items-center px-8">
    <div class="w-full md:w-1/2">
        <dotlottie-player src="https://lottie.host/2d4615b0-7ab1-4060-beb3-688246d5f96b/VXerSb5oP4.json"
            background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>
    </div>
    <div class="flex flex-col gap-4 md:gap-0 w-full md:w-1/2">
        <div class="flex items-center w-full">
            <dotlottie-player src="https://lottie.host/f129458b-b93e-4c6f-ad0b-01a77a82ca8f/FLeJOLlZWF.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
            </dotlottie-player>
            <dotlottie-player src="https://lottie.host/f129458b-b93e-4c6f-ad0b-01a77a82ca8f/FLeJOLlZWF.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
            </dotlottie-player>
            <dotlottie-player src="https://lottie.host/f129458b-b93e-4c6f-ad0b-01a77a82ca8f/FLeJOLlZWF.json"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop autoplay>
            </dotlottie-player>
        </div>
        @isset($currentThought)
        <div class="font-bold text-3xl italic h-24">
            "{{$currentThought}}"
        </div>
        @endisset
        @isset($currentProgress)
        <div class="w-full bg-gray-200 rounded-xl dark:bg-gray-700 mt-4">
            <div class="bg-secondary text-lg font-bold text-white text-center px-3 py-5 leading-none rounded-xl"
                style="width: {{$currentProgress}}%;">
                {{$currentProgress}}%
            </div>
        </div>
        @endisset
    </div>
</div>
