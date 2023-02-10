<div class="grid grid-cols-12 gap-6 py-12">
    <div class="col-span-2">Sidebar</div>
    <div class="col-span-10">
        <div class="flex flex-col gap-4">
            <label>Youtube url:</label>
            <input name="url" wire:model="url" class="p-3 rounded-lg" />
            <label>Language:</label>
            <select
                name="language"
                wire:model="language"
                class="p-3 rounded-lg"
            >
                <option value="en">English</option>
                <option value="pt">Portuguese</option>
            </select>
            <button
                wire:click="process"
                wire:loading.remove
                class="bg-zinc-300 px-2 py-1 rounded-lg"
            >
                Download
            </button>
            <div>{{ $language }}</div>
            <div wire:loading>Processing video...</div>
        </div>
    </div>
</div>
