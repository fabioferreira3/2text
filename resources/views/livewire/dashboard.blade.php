<div>
    <div class="mb-8 flex justify-between">
        @include('livewire.common.header', ['icon' => 'desktop-computer', 'label' => 'Dashboard'])
        <livewire:common.create-document />
    </div>
    <div class="p-6 bg-white rounded-lg">
        <div class='flex justify-between mb-6'>
            <h2 class="text-xl font-bold">My Documents</h2>
            <x-button href='/dashboard/trash' icon='trash' white label="Trash" />
        </div>
        <livewire:my-documents-table />
    </div>
</div>
