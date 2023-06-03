<div class="md:hidden">
    <x-dropdown persistent>
        @if($regenerateAction)
            <x-dropdown.item icon="refresh">
                <x-button sm spinner wire:loading.attr="disabled" wire:click='regenerate' label="AI Regenerate" class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
            </x-dropdown.item>@endif
        @if($historyAction)
            <x-dropdown.item icon="book-open" separator>
                <x-button sm wire:click='showHistoryModal' label="View History" class='border-0 px-0 text-zinc-700' />
            </x-dropdown.item>@endif
        @if($copyAction)
        <x-dropdown.item icon="clipboard-copy" separator>
            <x-button sm outline :disabled='$copied ? true : false' wire:click='copy' :label='$copied ? "Copied" : "Copy"' class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>@endif
        <x-dropdown.item icon="save" separator>
            <x-button sm spinner ire:loading.attr="disabled" wire:click='save' label='Save' class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>
    </x-dropdown>
</div>
<div class="hidden md:block">
    @if($regenerateAction)<x-button sm negative spinner wire:loading.attr="disabled" wire:click='regenerate' icon="refresh" label="AI Regenerate" class='rounded-lg' />@endif
    @if($historyAction)<x-button sm slate wire:click='showHistoryModal' icon="book-open" label="View History" class='rounded-lg' />@endif
    @if($copyAction) <x-button sm outline :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"' class='border border-gray-300 rounded-lg' />@endif
    <x-button sm dark spinner ire:loading.attr="disabled" wire:click='save' icon="save" label='Save' class='font-bold rounded-lg' />
</div>

