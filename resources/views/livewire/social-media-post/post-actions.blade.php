<div class="flex justify-end">
    <x-dropdown persistent>
        <x-dropdown.item icon="clipboard-copy">
            <x-button sm wire:loading.attr="disabled" wire:target="regenerate,save" wire:click='copy'
                :label="$copied ? __('social_media.copied') : __('social_media.copy')"
                class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>
        <x-dropdown.item icon="trash" separator>
            <x-button sm wire:loading.attr="disabled" wire:target="delete" wire:click='delete'
                label="{{ __('social_media.delete') }}"
                class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
        </x-dropdown.item>
    </x-dropdown>
</div>
