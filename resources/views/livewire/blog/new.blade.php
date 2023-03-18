<div class="flex flex-col gap-6">
    <div>
        <div class="flex flex-col gap-4">
            <h1 class="text-4xl font-bold">New blog post</h1>
        </div>
    </div>
    <div class="grid grid-cols-5 gap-6">
        <div class="col-span-2">
            <div class="p-4 bg-zinc-200 rounded">
                <h2 class="font-bold text-lg">Instructions</h2>
                <div class="flex flex-col gap-2 mt-2">
                    <div>Text</div>
                    <div>Text</div>
                    <div>Text</div>
                </div>
            </div>
        </div>
        <div class="col-span-3">
            <div class="flex flex-col gap-4 p-4 border rounded">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <label>Origin</label>
                        <select
                            name="provider"
                            wire:model="source_provider"
                            class="p-3 rounded-lg border border-zinc-200"
                        >
                            <option value="youtube">Youtube</option>
                            <option value="video_file">Video file</option>
                            <option value="free_text">Free text</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <label>Target keyword:</label>
                        <input
                            name="keyword"
                            wire:model="keyword"
                            class="p-3 rounded-lg border border-zinc-200"
                        />
                    </div>
                </div>

                @if ($source_provider === 'youtube')
                <div class="flex flex-col gap-3">
                    <label>Youtube url:</label>
                    <input
                        name="url"
                        wire:model="source_url"
                        class="p-3 border border-zinc-200 rounded-lg"
                    />
                </div>
                @endif @if ($source_provider === 'free_text')
                <div class="flex flex-col gap-3">
                    <label>Text:</label>
                    <textarea
                        class="border border-zinc-200 rounded-lg"
                        rows="10"
                        wire:model="free_text"
                    ></textarea>
                </div>
                @endif

                <div class="flex flex-col gap-3">
                    <label>Language:</label>
                    <select
                        name="language"
                        wire:model="language"
                        class="p-3 rounded-lg border border-zinc-200"
                    >
                        <option value="en">English</option>
                        <option value="pt">Portuguese</option>
                    </select>
                </div>
                <div class="flex flex-col gap-3">
                    <label>Tone:</label>
                    <select
                        name="tone"
                        wire:model="tone"
                        class="p-3 rounded-lg border border-zinc-200"
                    >
                        <option value="">Default</option>
                        <option value="funny">Funny</option>
                        <option value="academic">Academic</option>
                        <option value="dramatic">Dramatic</option>
                        <option value="misterious">Misterious</option>
                        <option value="optimistic">Optimistic</option>
                        <option value="pessimistic">Pessimistic</option>
                    </select>
                </div>
                <div class="flex justify-center mt-4">
                    <button
                        wire:click="process"
                        wire:loading.remove
                        class="bg-red-700 text-white font-bold px-4 py-2 rounded-lg"
                    >
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
