<div class="flex flex-col gap-2 border p-4 bg-zinc-200 rounded-lg">
    <div class="flex justify-between">
        <label for="title" class="font-bold text-xl">Title</label>
        <div class="flex gap-2">
            <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Regenerate</button>
            <button wire:click='showHistoryModal' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">View history</button>
            <button {{ $copied ? 'disabled' : '' }} wire:click='copy' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">{{ $copied ? 'Copied!' : 'Copy' }}</button>
            <button wire:click='save' class="px-3 py-2 bg-black rounded-lg text-white text-sm">Save</button>
        </div>
    </div>
    <x-input placeholder="Post title" class="p-3 rounded-lg border border-zinc-200" wire:model="content" type="text" name="title"/>
</div>
