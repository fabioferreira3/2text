<div class="flex flex-col">
    <div class='flex items-center bg-white rounded-t-lg border border-zinc-200 p-4'>
        <img class="h-12" src="{{ Vite::asset("resources/images/$platform-logo.png") }}">
    </div>
    <div class="border-l border-r border-b border-zinc-200 rounded-b-lg overflow-hidden flex-grow">
        @livewire("social-media-post.platforms.$platform-post", [$post], key($post->id))
    </div>
</div>
