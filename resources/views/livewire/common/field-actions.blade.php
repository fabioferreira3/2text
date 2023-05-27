<x-button spinner wire:loading.attr="disabled" wire:click='regenerate' icon="refresh" label="AI Regenerate" class='border border-gray-300 bg-white rounded-lg' />
<x-button wire:click='showHistoryModal' icon="book-open" label="View History" class='rounded-lg bg-slate-600 text-white' />
<x-button outline :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"' class='border border-gray-300 rounded-lg' />
<x-button spinner red wire:click='save' icon="save" label='Save' class='font-bold rounded-lg' />
