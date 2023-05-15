<div class="flex flex-col gap-12">
    <div class="flex flex-col gap-2 border p-4 bg-zinc-200 rounded-lg">
        <div class="flex justify-between">
            <label for="title" class="font-bold text-xl">Title</label>
            <div class="flex gap-2">
                <button wire:click='regenerateTitle' class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Regenerate</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">View history</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Copy</button>
                <button wire:click='saveTitle' class="px-3 py-2 bg-black rounded-lg text-white text-sm">Save</button>
            </div>
        </div>
        <input class="p-3 rounded-lg border border-zinc-200" wire:model="title" type="text" name="title"/>
    </div>
    <div class="flex flex-col gap-2 border p-4 bg-zinc-200 rounded-lg">
        <div class="flex justify-between">
            <label for="title" class="font-bold text-xl">Meta description</label>
            <div class="flex gap-2">
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Regenerate</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">View history</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Copy</button>
                <button class="px-2 py-1 bg-black rounded-lg text-white text-sm">Save</button>
            </div>
        </div>
        <textarea class="rounded-lg border border-zinc-200" name="meta_description" wire:model="meta_description" rows="2"></textarea>
    </div>
    <div class="flex flex-col gap-2 border p-4 bg-zinc-200 rounded-lg">
        <div class="flex justify-between">
            <label for="title" class="font-bold text-xl">Content</label>
            <div class="flex gap-2">
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Regenerate</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">View history</button>
                <button class="px-3 py-2 bg-zinc-300 rounded-lg text-zinc-800 text-sm">Copy</button>
                <button class="px-2 py-1 bg-black rounded-lg text-white text-sm">Save</button>
            </div>
        </div>
        <textarea class="editor" name="context" wire:model="content" rows="30"></textarea>
    </div>
</div>

@stack('scripts')
<script src="https://cdn.tiny.cloud/1/k28s5ifm759tzn9kbhh4i2dr9zo14vac4redj48xvb3shald/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
      selector: '.editor',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: '',
      mergetags_list: []
    });
  </script>
