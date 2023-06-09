<div>
    <div class="mb-8 flex justify-between">
        @include('livewire.common.header', ['icon' => 'desktop-computer', 'label' => __('dashboard.dashboard')])
        <livewire:common.create-document />
    </div>
    <div class="p-6 bg-white rounded-lg">
        <div class='flex justify-between mb-6'>
            <h2 class="text-xl font-bold text-zinc-700">{{ __('dashboard.my_documents') }}</h2>
            <x-button href='/dashboard/trash' icon='trash' white label="{{__('dashboard.trash')}}" />
        </div>
        <livewire:my-documents-table />
    </div>
</div>