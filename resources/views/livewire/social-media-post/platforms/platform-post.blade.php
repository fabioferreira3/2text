<div class="flex flex-col">
    <div class='flex items-center justify-between bg-white rounded-t-xl border border-zinc-200 px-6 py-4'>
        <img class="h-12" src="{{ Vite::asset(" resources/images/$platform-logo.png") }}">
        <div class="flex justify-end">
            <x-dropdown persistent>
                <x-dropdown.item icon="refresh">
                    <x-button sm wire:loading.attr="disabled" wire:click='regenerate'
                        label="{{ __('common.regenerate') }}"
                        class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
                </x-dropdown.item>
                <x-dropdown.item icon="clipboard-copy" separator>
                    <x-button sm wire:loading.attr="disabled" wire:target="regenerate,save" @if($copied) disabled @endif
                        wire:click='copy' :label='$copied ? __(' common.copied') : __('common.copy')'
                        class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
                </x-dropdown.item>
                <x-dropdown.item icon="save" separator>
                    <x-button sm spinner="save" wire:loading.attr="disabled" wire:target="regenerate,save"
                        wire:click='save' label="{{ __('common.save') }}"
                        class='hover:bg-transparent hover:shadow-none border-0 px-0 text-zinc-700' />
                </x-dropdown.item>
            </x-dropdown>
        </div>
    </div>
    <div class="border-l border-r border-b border-zinc-200 rounded-b-xl overflow-hidden flex-grow">
        @livewire("social-media-post.platforms.$platform-post", [$post], key($post->id))
    </div>
</div>