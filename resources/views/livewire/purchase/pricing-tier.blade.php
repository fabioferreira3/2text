<div class="relative p-4 border border-gray-200 bg-main rounded-xl overflow-hidden">
    <div class="absolute top-0 right-0 text-white bg-secondary p-3 rounded-full -mr-10 -mt-10 w-32 h-32">
        <div class="flex flex-col px-4 mt-10 font-bold">
            <div class="text-2xl">{{$product->meta['off']}}</div>
            <div>OFF</div>
        </div>
    </div>
    <div class="flex flex-col gap-2 p-2 rounded-xl text-white">
        <div class="text-2xl font-bold text-secondary text-start">{{$product->label}}</div>
        <div class="text-5xl font-bold">${{$product->meta['price']}}</div>
        <div class="text-sm">{{__('checkout.billed_monthly')}}</div>
        <div class="flex flex-col gap-2 text-sm">
            @foreach($product->meta['features'] as $feature)
            <div class="flex items-center gap-2">
                <x-icon name="check" class="w-4 h-4 text-secondary" />
                <div class="">{{$feature}}</div>
            </div>
            @endforeach
        </div>
        <button wire:click="selectProduct('{{$product->id}}')"
            class="cursor-pointer flex items-center justify-between gap-4 bg-secondary rounded-full pl-3 pr-1 py-1 mt-4 text-lg">
            <div class="font-bold">{{__('checkout.get')}} {{$product->label}}</div>
            <div class="bg-white rounded-full p-2">
                <x-icon name="chevron-double-right" width="18" height="18" class="text-secondary" />
            </div>
        </button>
    </div>
</div>
