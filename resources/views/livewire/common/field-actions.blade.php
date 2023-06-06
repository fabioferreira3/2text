<div class="md:hidden flex justify-end">
    <x-dropdown persistent>
        @if($regenerateAction)
            <x-dropdown.item icon="refresh">
                <x-button sm spinner="regenerate" wire:loading.attr="disabled" wire:click='regenerate' label="Regenerate" class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
            </x-dropdown.item>
        @endif

        @if($historyAction)
            <x-dropdown.item icon="book-open" separator>
                <x-button sm wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='showHistoryModal' label="View History" class='border-0 px-0 text-zinc-700' />
            </x-dropdown.item>
        @endif

        @if($copyAction)
        <x-dropdown.item icon="clipboard-copy" separator>
            <x-button sm outline wire:loading.attr="disabled" wire:target="regenerate,save" :disabled='$copied ? true : false' wire:click='copy' :label='$copied ? "Copied" : "Copy"' class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>
        @endif
        <x-dropdown.item icon="save" separator>
            <x-button sm spinner="save" wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='save' label='Save' class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>
    </x-dropdown>
</div>
<div class="hidden md:block">
    @if($regenerateAction)<x-button sm amber spinner="regenerate" wire:loading.attr="disabled" wire:click='regenerate' icon="refresh" label="Regenerate" class='rounded-lg' />@endif
    @if($historyAction)<x-button sm slate wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='showHistoryModal' icon="book-open" label="View History" class='rounded-lg' />@endif
    @if($copyAction) <x-button sm outline wire:loading.attr="disabled" wire:target="regenerate,save" :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"' class='bg-zinc-200 border text-zinc-700 border-gray-400 rounded-lg' />@endif
    <x-button sm dark spinner="save" wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='save' icon="save" label='Save' class='font-bold rounded-lg' />
</div>

