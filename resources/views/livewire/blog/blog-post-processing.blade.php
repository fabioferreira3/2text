<div>
    <div class="md:mb-8">
        @include('livewire.common.label', ['title' => $title])
    </div>
    <div class="md:mt-24">
        @include('livewire.common.processing-robot', [
        'currentThought' => $currentThought,
        'currentProgress' => $currentProgress
        ])
    </div>
</div>