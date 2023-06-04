<div>
    <div class="mb-8 flex justify-between">
        @include('livewire.common.header', ['icon' => 'trash', 'label' => 'Trash'])
    </div>
    <div class="p-6 bg-white rounded-lg">
        <div class='mb-6'>
            <x-button href='/dashboard' icon='desktop-computer' white label="Back to dashboard" />
        </div>
        <livewire:trash-table />
    </div>
</div>
