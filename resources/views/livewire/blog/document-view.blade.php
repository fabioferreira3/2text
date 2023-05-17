<div class="flex flex-col gap-12">
    <div class="flex flex-col gap-2 border p-4 bg-zinc-200 rounded-lg">
        <div class="flex justify-between">
            <label for="title" class="font-bold text-xl">Title</label>
            <div class="flex gap-2">
                <button wire:click='regenerateTitle' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Regenerate</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">View history</button>
                <button {{ $copied === 'title' ? 'disabled' : '' }} wire:click='copyTitle' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">{{ $copied === 'title' ? 'Copied!' : 'Copy' }}</button>
                <button wire:click='saveTitle' class="px-3 py-2 bg-black rounded-lg text-white text-sm">Save</button>
            </div>
        </div>
        <x-input placeholder="Post title" class="p-3 rounded-lg border border-zinc-200" wire:model="title" type="text" name="title"/>
    </div>
    <div class="flex flex-col gap-2 border p-4 bg-zinc-200 rounded-lg">
        <div class="flex justify-between">
            <label for="title" class="font-bold text-xl">Meta description</label>
            <div class="flex gap-2">
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Regenerate</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">View history</button>
                <button {{ $copied === 'meta_description' ? 'disabled' : '' }} wire:click='copyMetaDescription' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">{{ $copied === 'meta_description' ? 'Copied!' : 'Copy' }}</button>
                <button wire:click='saveMetaDescription' class="px-2 py-1 bg-black rounded-lg text-white text-sm">Save</button>
            </div>
        </div>
        <x-textarea class="rounded-lg border border-zinc-200" name="meta_description" wire:model="meta_description" rows="2"></x-textarea>
    </div>
    @livewire('blog.content-editor', [$document])
</div>
<div class="fixed top-1/3 left-1/2 bg-secondary w-48 h-48 z-10">Modal</div>

@stack('scripts')
