<div class="fixed right-0 top-0 bottom-0 flex flex-col bg-white border border-gray-200 w-1/3 h-screen p-8">
    <div class="flex justify-start items-center gap-4">
        <x-icon name="question-mark-circle" class="w-12 h-12 text-secondary" />
        <div class="flex flex-col text-start">
            <h3 class="font-bold text-gray-700"><span class="text-4xl">{{__('help.help')}}</h3>
            <h4 class="font-medium text-2xl">{{$title}}</h4>
        </div>
    </div>
    <div class="mt-12">
        {{$content}}
    </div>
</div>
