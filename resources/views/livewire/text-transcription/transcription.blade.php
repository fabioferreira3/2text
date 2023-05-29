<div class="flex flex-col gap-12">
    <div class="flex flex-col gap-2 p-4 bg-zinc-200 rounded-lg border border-gray-300">
        <div class="flex justify-between mb-2">
            <label for="content" class="font-bold text-xl">Content</label>
            <div class="flex gap-2">
                @include('livewire.common.field-actions', ['copyAction' => false, 'regenerateAction' => false, 'historyAction' => true])
            </div>
        </div>
        <textarea class="editor" name="context" rows="30">{{$content}}</textarea>
    </div>
    @if($displayHistory)
    @livewire('common.history-modal', [$document])
    @endif
</div>

<script src="https://cdn.tiny.cloud/1/k28s5ifm759tzn9kbhh4i2dr9zo14vac4redj48xvb3shald/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    function initEditor() {
        setTimeout(function() {
            tinymce.init({
                selector: '.editor',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                tinycomments_mode: 'embedded',
                tinycomments_author: '',
                mergetags_list: [],
                setup: function(editor) {
                    editor.on('blur', function(e) {
                        if (editor.isDirty()) {
                            window.livewire.emit('editorUpdated', editor.getContent());
                        }
                    });
                }
            });
        }, 100);
    }

    initEditor();

    document.addEventListener('livewire:load', function() {
        window.livewire.on('updateContent', function() {
            initEditor();
        });
        window.livewire.on('refreshEditor', function() {
            initEditor();
        });
    });
</script>


@stack('scripts')