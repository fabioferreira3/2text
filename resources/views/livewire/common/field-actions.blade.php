<div class="md:hidden">
    <x-dropdown persistent>
        @if($regenerateAction)<x-dropdown.item icon="refresh" wire:loading.attr="disabled" wire:click='regenerate' label="AI Regenerate" />@endif
        @if($historyAction)<x-dropdown.item icon="user" separator label="View History" />@endif
        @if($copyAction) <x-dropdown.item icon="clipboard-copy" wire:click='copy' separator :label='$copied ? "Copied" : "Copy"' :disabled='$copied ? true : false' />@endif
        <x-dropdown.item icon="user" separator label="Save" />
    </x-dropdown>
</div>
<div class="hidden md:block">
    @if($regenerateAction)<x-button sm negative spinner wire:loading.attr="disabled" wire:click='regenerate' icon="refresh" label="AI Regenerate" class='rounded-lg' />@endif
    @if($historyAction)<x-button sm slate wire:click='showHistoryModal' icon="book-open" label="View History" class='rounded-lg' />@endif
    @if($copyAction) <x-button sm outline :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"' class='border border-gray-300 rounded-lg' />@endif
    <x-button sm dark spinner wire:click='save' icon="save" label='Save' class='font-bold rounded-lg' />
</div>

