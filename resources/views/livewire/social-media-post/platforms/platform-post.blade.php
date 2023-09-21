<div class="flex flex-col">
    <div class='flex items-center bg-white rounded-t-xl border border-zinc-200 px-6 py-4'>
        <img class="h-12" src="{{ Vite::asset("resources/images/$platform-logo.png") }}">
    </div>
    <div class="border-l border-r border-b border-zinc-200 rounded-b-xl overflow-hidden flex-grow">
        @livewire("social-media-post.platforms.$platform-post", [$post], key($post->id))
    </div>
</div>
