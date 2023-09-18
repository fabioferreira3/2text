@if ($status->value === 'finished')
    <x-badge icon="check-circle" class="font-sans py-1" md outline emerald :label="$status->label()">
        <x-slot name="prepend" class="relative flex items-center">
        </x-slot>
    </x-badge>
@elseif ($status->value === 'in_progress')
    <x-badge class="font-sans border border-gray-300 py-1" md label="In Progress">
        <x-slot name="prepend" class="relative flex items-center w-2 h-2">
            <span class="absolute inline-flex w-full h-full rounded-full opacity-75 bg-secondary animate-ping"></span>
            <span class="relative inline-flex w-2 h-2 rounded-full bg-secondary"></span>
        </x-slot>
    </x-badge>
@elseif ($status->value === 'failed')
    <x-badge icon="minus-circle" class="font-sans border border-gray-300 py-1" outline negative md label="Failed">
        <x-slot name="prepend" class="relative flex items-center w-2 h-2"></x-slot>
    </x-badge>
@elseif ($status->value === 'aborted')
    <x-badge icon="x" class="font-sans border border-gray-300 py-1" outline negative md label="Aborted">
        <x-slot name="prepend" class="relative flex items-center w-2 h-2"></x-slot>
    </x-badge>
@else
    <x-badge icon="clock" class="font-sans py-1 border border-gray-300" md label="Draft">
        <x-slot name="prepend" class="relative flex items-center w-2 h-2"></x-slot>
    </x-badge>
@endif
