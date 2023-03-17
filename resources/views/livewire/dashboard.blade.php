<div class="flex flex-col gap-6">
    <div>
        <button
            wire:click="newPost"
            class="px-6 py-2 bg-red-700 text-white rounded"
        >
            New blog post
        </button>
    </div>
    <div class="flex flex-col gap-4">
        <label>Youtube url:</label>
        <input name="url" wire:model="source_url" class="p-3 rounded-lg" />
        <label>Target keyword:</label>
        <input name="keyword" wire:model="keyword" class="p-3 rounded-lg" />
        <label>Language:</label>
        <select name="language" wire:model="language" class="p-3 rounded-lg">
            <option value="en">English</option>
            <option value="pt">Portuguese</option>
        </select>
        <label>Tone:</label>
        <select name="tone" wire:model="tone" class="p-3 rounded-lg">
            <option value="">Default</option>
            <option value="Funny">Funny</option>
            <option value="Academic">Academic</option>
            <option value="Dramatic">Dramatic</option>
            <option value="Misterious">Misterious</option>
            <option value="Optimistic">Optimistic</option>
            <option value="Pessimistic">Pessimistic</option>
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
