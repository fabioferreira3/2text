<div class="flex flex-col gap-2 border p-4 bg-zinc-200 rounded-lg">
    <div class="flex justify-between">
        <label for="title" class="font-bold text-xl">Meta description</label>
        <div class="flex gap-2">
            <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Regenerate</button>
            <button wire:click='showHistoryModal' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">View history</button>
            <button {{ $copied ? 'disabled' : '' }} wire:click='copy' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">{{ $copied ? 'Copied!' : 'Copy' }}</button>
            <button wire:click='save' class="px-2 py-1 bg-black rounded-lg text-white text-sm">Save</button>
        </div>
    </div>
    <x-textarea class="rounded-lg border border-zinc-200" name="meta_description" wire:model="content" rows="2"></x-textarea>
</div>
