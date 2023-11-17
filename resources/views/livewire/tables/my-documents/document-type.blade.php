@if ($type->value === \App\Enums\DocumentType::AUDIO_TRANSCRIPTION->value)
<x-badge icon="chat-alt" class="font-thin" md amber label="{{ $type->label() }}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if ($type->value === \App\Enums\DocumentType::BLOG_POST->value)
<x-badge icon="newspaper" class="font-thin" md positive label="{{ $type->label() }}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if ($type->value === \App\Enums\DocumentType::PARAPHRASED_TEXT->value)
<x-badge icon="switch-horizontal" class="font-thin" md fuchsia label="{{ $type->label() }}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if ($type->value === \App\Enums\DocumentType::SOCIAL_MEDIA_GROUP->value)
<x-badge icon="hashtag" class="font-thin" md blue label="{{ $type->label() }}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if ($type->value === \App\Enums\DocumentType::SUMMARIZER->value)
<x-badge icon="sort-ascending" class="font-thin" md fuchsia label="{{ $type->label() }}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif

@if ($type->value === \App\Enums\DocumentType::TEXT_TO_SPEECH->value)
<x-badge icon="volume-up" class="font-thin" md lime label="{{ $type->label() }}">
    <x-slot name="prepend" class="relative flex items-center">
    </x-slot>
</x-badge>
@endif