<div class="flex flex-col gap-2 p-4 bg-zinc-200 rounded-lg border border-gray-300">
    <div class="flex justify-between">
        <label for="title" class="font-bold text-xl">Title</label>
        <div class="flex gap-2">
            <x-button wire:loading.attr="disabled" white wire:click='regenerate' icon="refresh" label="Regenerate" class='border border-gray-300 rounded-lg'/>
            <x-button wire:click='showHistoryModal' icon="book-open" label="View History" class='border border-gray-300 rounded-lg'/>
            <x-button :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? "Copied" : "Copy"'  class='border border-gray-300 rounded-lg'/>
            <x-button wire:click='save' icon="save" dark label='Save' class='border border-gray-300 rounded-lg'/>
        </div>
    </div>
    <x-input placeholder="Post title" class="mt-2 p-3 rounded-lg border border-zinc-200" wire:model="content" type="text" name="title"/>
</div>
