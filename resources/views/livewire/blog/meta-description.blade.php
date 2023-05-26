<div class="flex flex-col gap-2 p-4 bg-zinc-200 rounded-lg border border-gray-300">
    <div class="flex justify-between">
        <label for="title" class="font-bold text-xl">Meta description</label>
        <div class="flex gap-2">
            <x-button wire:loading.attr="disabled" white wire:click='regenerate' icon="refresh" label="Regenerate" class='border border-gray-300 rounded-lg'/>
            <x-button wire:click='showHistoryModal' icon="book-open" label="View History" class='border border-gray-300 rounded-lg'/>
            <x-button :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"'  class='border border-gray-300 rounded-lg'/>
            <x-button wire:click='save' icon="save" dark label='Save' class='border border-gray-300 rounded-lg'/>
        </div>
    </div>
    <x-textarea class="mt-2 rounded-lg border border-zinc-200" name="meta_description" wire:model="content" rows="2"></x-textarea>
</div>
