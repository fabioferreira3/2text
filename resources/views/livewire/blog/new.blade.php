<div class="flex flex-col gap-6">
    <div>
        <div class="flex flex-col gap-4">
            <h1 class="text-4xl font-bold">New blog post</h1>
        </div>
    </div>
    <div class="grid grid-cols-5 gap-6">
        <div class="col-span-3">
            <div class="flex flex-col gap-4 p-4 border rounded">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-3">
                        <label>Source</label>
                        <select
                            name="provider"
                            wire:model="source"
                            class="p-3 rounded-lg border border-zinc-200"
                        >
                            <option value="youtube">Youtube</option>
                            <option value="video_file">Video file</option>
                            <option value="free_text">Free text</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-3">
                        <label>Keyword:</label>
                        <input
                            name="keyword"
                            wire:model="keyword"
                            class="p-3 rounded-lg border border-zinc-200"
                        />
                    </div>
                </div>

                @if ($source === 'youtube')
                <div class="flex flex-col gap-3">
                    <label>Youtube url:</label>
                    <input
                        name="url"
                        wire:model="source_url"
                        class="p-3 border border-zinc-200 rounded-lg"
                    />
                </div>
                @endif @if ($source === 'free_text')
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label>Context:</label>
                        <small>Briefly describe the main topic of the post using at least 100 words</small>
                        <small>You could also paste a larger text to be used as context</small>
                    </div>

                    <textarea
                        class="border border-zinc-200 rounded-lg"
                        rows="10"
                        maxlength="30000"
                        wire:model="context"
                    ></textarea>
                </div>
                @endif
                <div class="flex flex-col gap-3">
                    <label>Subtopics:</label>
                    <input
                        type="number"
                        name="target_headers_count"
                        wire:model="targetHeadersCount"
                        class="p-3 rounded-lg border border-zinc-200"
                    />
                </div>
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
                        <option value="academic">Academic</option>
                        <option value="adventurous">Adventurous</option>
                        <option value="casual">Casual</option>
                        <option value="dramatic">Dramatic</option>
                        <option value="formal">Formal</option>
                        <option value="funny">Funny</option>
                        <option value="misterious">Misterious</option>
                        <option value="pessimistic">Pessimistic</option>
                        <option value="optimistic">Optimistic</option>
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
    </div>
    </div>
