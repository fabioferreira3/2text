<x-button sm negative spinner wire:loading.attr="disabled" wire:click='regenerate' icon="refresh" label="AI Regenerate" class='rounded-lg' />
<x-button sm slate wire:click='showHistoryModal' icon="book-open" label="View History" class='rounded-lg' />
<x-button sm outline :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"' class='border border-gray-300 rounded-lg' />
<x-button sm dark spinner wire:click='save' icon="save" label='Save' class='font-bold rounded-lg' />
