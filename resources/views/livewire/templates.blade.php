<div>
    <div class="flex flex-col gap-2 mb-8">
        <h1 class="text-4xl font-bold mb-6">Templates</h1>
        <h2 class="text-3xl font-bold">Start here</h1>
        <small class="text-lg font-thin">Choose a template and let's write some content!</small>
    </div>
    <div class="grid md:grid-cols-3 xl:grid-cols-4 gap-6">
        <div wire:click="newBlogPost" class="flex flex-col gap-3 p-6 border bg-white rounded-lg hover:border-secondary hover:cursor-pointer">
            <x-icon name="newspaper" class="text-secondary w-16 h-16" />
            <div class="font-bold text-xl">Blog post</div>
            <div>Create a full SEO friendly article with the help of AI</div>
        </div>
    </div>
</div>
