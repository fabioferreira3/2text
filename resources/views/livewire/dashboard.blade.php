<div>
    <div class="mb-8 flex justify-between">
        @include('livewire.common.header', ['icon' => 'desktop-computer', 'label' => 'Dashboard'])
        <livewire:common.create-document />
    </div>
    <div class="p-6 bg-white rounded-lg">
        <h2 class="text-xl font-bold mb-6">My Documents</h2>
        <livewire:my-documents-table />
    </div>
</div>
