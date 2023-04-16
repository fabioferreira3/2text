<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <label for="title" class="font-bold text-xl">Title</label>
        <input class="p-3 rounded-lg border border-zinc-200" value="{{$document->meta['title']}}" type="text" name="title"/>

    </div>
    <textarea class="editor">
        {{$content}}
     </textarea>
     <div class="flex flex-col gap-2">
        <label for="meta_description" class="font-bold text-xl">Meta Description</label>
        <textarea class="p-3 rounded-lg border border-zinc-200" name="meta_description">
            {{$document->meta['meta_description']}}
        </textarea>

    </div>
</div>

@stack('scripts')
<script src="https://cdn.tiny.cloud/1/k28s5ifm759tzn9kbhh4i2dr9zo14vac4redj48xvb3shald/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
      selector: '.editor',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: '',
      mergetags_list: []
    });
  </script>
