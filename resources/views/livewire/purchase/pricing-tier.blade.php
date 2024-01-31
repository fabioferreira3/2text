<div class="p-4 border border-gray-200 bg-main rounded-xl">
    <div class="flex flex-col gap-2 p-2 rounded-xl text-white">
        <div class="text-2xl font-bold text-secondary text-start">{{$product->label}}</div>
        <div class="text-5xl font-bold">${{$product->meta['price']}}</div>
        <div class="text-sm">billed monthly</div>
        <div class="flex flex-col gap-2">
            @foreach($product->meta['features'] as $feature)
            <div class="flex items-center gap-2">
                <x-icon name="check" class="w-4 h-4 text-secondary" />
                <div class="">{{$feature}}</div>
            </div>
            @endforeach
        </div>
        <button wire:click="selectProduct('{{$product->id}}')" class="cursor-pointer flex items-center justify-between gap-4 bg-secondary rounded-full pl-3 pr-1 py-1 mt-4 text-lg">
            <div class="font-bold">Get {{$product->label}}</div>
            <div class="bg-white rounded-full p-2">
                <x-icon name="chevron-double-right" width="18" height="18" class="text-secondary" />
            </div>
        </button>
    </div>
</div>