<div>
    <a href="{{ route('tools') }}" class="flex items-center gap-2 bg-secondary text-white px-3 py-2 rounded-lg">
        <x-icon name="plus" color="white" width="24" height="24" />
        <span class="font-bold text-lg">{{__('dashboard.new')}}</span>
    </a>
</div>
{{--
<x-button neutral href="{{ route('tools') }}" icon='plus'
    class="hidden md:flex bg-secondary hover:bg-main h-full text-lg rounded-lg text-white"
    label="{{__('dashboard.new')}}" />
<x-button.circle href="{{ route('tools') }}" icon="plus"
    class="md:hidden bg-secondary hover:bg-black font-bold text-zinc-100" /> --}}
