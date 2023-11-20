<div>
    <div class="mb-8 flex justify-between">
        @include('livewire.common.header', ['icon' => 'trash', 'title' => __('dashboard.trash')])
    </div>
    <div class="p-6 bg-white rounded-lg">
        <div class='mb-6'>
            <x-button href='/dashboard' icon='desktop-computer' white label="{{__('dashboard.back_dashboard')}}" />
        </div>
        <livewire:trash-table />
    </div>
</div>