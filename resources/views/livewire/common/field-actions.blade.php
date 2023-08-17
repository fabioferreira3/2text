<div class="md:hidden flex justify-end">
    <x-dropdown persistent>
        @if ($regenerateAction)
            <x-dropdown.item icon="refresh">
                <x-button sm wire:loading.attr="disabled" wire:click='regenerate' label="{{__('common.regenerate')}}" class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
            </x-dropdown.item>
        @endif

        @if ($historyAction)
            <x-dropdown.item icon="book-open" separator>
                <x-button sm wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='showHistoryModal' label="{{__('common.view_history')}}" class='border-0 px-0 text-zinc-700' />
            </x-dropdown.item>
        @endif

        @if ($copyAction)
        <x-dropdown.item icon="clipboard-copy" separator>
            <x-button sm wire:loading.attr="disabled" wire:target="regenerate,save" :disabled='$copied ? true : false' wire:click='copy' :label='$copied ? __("common.copied") : __("common.copy")' class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>
        @endif
        <x-dropdown.item icon="save" separator>
            <x-button sm spinner="save" wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='save' label="{{__('common.save')}}" class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>
    </x-dropdown>
</div>
<div class="hidden md:flex items-center gap-2">
    @if($regenerateAction)<x-button neutral sm spinner="isProcessing" wire:loading.attr="disabled" wire:click='regenerate' icon="refresh" label="{{__('common.regenerate')}}" class='rounded-lg bg-secondary hover:bg-main text-white border-0' />@endif
    @if($historyAction)<x-button sm wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='showHistoryModal' icon="book-open" label="{{__('common.view_history')}}" class='bg-zinc-100 hover:bg-zinc-400 hover:text-white text-zinc-700 rounded-lg border-primary border-opacity-20' />@endif
    @if($copyAction) <x-button sm wire:loading.attr="disabled" wire:target="regenerate,save" :disabled='$copied ? true : false' wire:click='copy' icon="clipboard-copy" :label='$copied ? __("common.copied") : __("common.copy")' class='bg-zinc-100 hover:bg-zinc-400 hover:text-white text-zinc-700 rounded-lg border-primary border-opacity-20' />@endif
    <x-button neutral sm spinner="save" wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='save' icon="save" label="{{__('common.save')}}" class='font-bold rounded-lg text-white bg-main hover:bg-secondary' />
    @if ($isProcessing)
        <div class="ml-4">
            <x-loader/>
        </div>
    @endif
</div>

