<div class="flex justify-between space-x-4">
    <div class="w-1/2">
        <textarea id="inputText" class="w-full h-full p-2 border rounded" wire:model="inputText" wire:keydown.enter.prevent="paraphraseSentence(array_key_last(sentences))">
        </textarea>
        <div id="inputContainer" class="p-2 border rounded mt-4">
            @foreach($sentences as $hash => $sentence)
            <p onclick="paraphraseSentence('{{ $hash }}')" class="cursor-pointer">{{ $sentence }}</p>
            @endforeach
        </div>
    </div>
    <div class="w-1/2">
        <div id="outputContainer" class="p-2 border rounded">
            @foreach($outputSentences as $sentence)
            <p>{{ $sentence }}</p>
            @endforeach
        </div>
    </div>
</div>

<script>
    function paraphraseSentence(hash) {
        @this.call('paraphraseSentence', hash);
    }
</script>