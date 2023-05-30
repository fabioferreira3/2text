@if($type->value === 'blog_post')
<x-badge icon="newspaper" md positive label="{{$type->label()}}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if($type->value === 'text_transcription')
<x-badge icon="chat-alt" md amber label="{{$type->label()}}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

