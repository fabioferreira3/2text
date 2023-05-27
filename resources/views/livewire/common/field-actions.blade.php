<x-button white wire:loading.attr="disabled" wire:click='regenerate' icon="refresh" label="Regenerate" class='border border-gray-300 rounded-lg' />
<x-button slate wire:click='showHistoryModal' icon="book-open" label="View History" class='border border-gray-300 rounded-lg' />
<x-button dark outline :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"' class='border border-gray-300 rounded-lg' />
<x-button red wire:click='save' icon="save" label='Save' class='font-bold rounded-lg' />