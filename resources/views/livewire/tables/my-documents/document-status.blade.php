@if($isCompleted)
<x-badge md outline emerald  label="Finished">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@else
<x-badge md outline dark label="In Progress">
    <x-slot name="prepend" class="relative flex items-center w-2 h-2">
        <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-slate-500 animate-ping"></span>
        <span class="relative inline-flex w-2 h-2 rounded-full bg-slate-500"></span>
    </x-slot>
</x-badge>
@endif
