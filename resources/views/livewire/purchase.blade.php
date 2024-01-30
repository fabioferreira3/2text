<div class="flex flex-col justify-center items-center">
    <div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-lg">
        <div class="md:flex">
            <div class="w-full p-4 px-5 py-5">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-700">{{__('checkout.purchase_units')}}</h2>
                    <p class="text-gray-400">{{__('checkout.individual_price')}}</p>
                </div>
                <form class="flex flex-col gap-6" wire:submit.prevent="processPurchase">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-col justify-center">
                            <label for="units" class="text-gray-500 font-semibold">{{__('checkout.enter_units')}}:</label>
                            <input type="number" wire:model="units" id="units" class="border border-gray-300 h-12 w-full rounded-lg focus:outline-none focus:ring-2 focus:ring-[#EA1F88] text-lg text-gray-600 px-3 mt-2">
                            @if($errors->has('units'))
                            <span class="text-red-500 text-sm">{{ $errors->first('units') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            Total: <div class="font-bold text-lg">${{$totalPrice}}</div>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="w-full max-w-xs text-lg font-semibold shadow-sm rounded-lg py-3 bg-[#EA1F88] text-white hover:bg-[#c01770] focus:outline-none focus:ring-2 focus:ring-[#080B53] transition ease-in duration-200">
                            {{__('checkout.next')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>