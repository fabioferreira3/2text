@if($type->value === 'blog_post')
<x-badge icon="newspaper" class="font-thin" md positive label="{{$type->label()}}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if($type->value === 'text_transcription')
<x-badge icon="chat-alt" class="font-thin" md amber label="{{$type->label()}}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if($type->value === 'social_media_post')
<x-badge icon="hashtag" class="font-thin" md blue label="{{$type->label()}}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if($type->value === 'paraphrased_text')
<x-badge icon="switch-horizontal" class="font-thin" md fuchsia label="{{$type->label()}}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if($type->value === 'text_to_speech')
<x-badge icon="volume-up" class="font-thin" md lime label="{{$type->label()}}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

