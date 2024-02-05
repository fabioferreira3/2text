<div>
    <div class="flex flex-col gap-12 justify-center items-center w-2/3 mx-auto">
        <div class="bg-white rounded-lg overflow-hidden w-full">
            <div class="flex">
                <div class="flex flex-col justify-center items-center w-full p-4 px-5 py-5">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-700">{{__('checkout.purchase_units')}}</h2>
                        <p class="text-gray-400">{{__('checkout.individual_price')}}</p>
                    </div>
                    <form class="flex flex-col gap-2" wire:submit="processPurchase">
                        <div class="flex flex-col gap-2">
                            <div class="flex flex-col gap-2 justify-center">
                                <label for="units"
                                    class="text-gray-500 font-semibold">{{__('checkout.enter_units')}}:</label>
                                <button wire:click="$toggle('displayCalculator')" type="button"
                                    class="text-gray-500 text-center text-xs underline">Not sure how
                                    much you need?
                                </button>
                                <input min="0" max="10000" type="number" wire:model.live="units" id="units"
                                    class="border border-gray-300 text-center h-12 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#EA1F88] text-lg text-gray-600 px-3 mt-2">
                                @if($errors->has('units'))
                                <span class="text-red-500 text-sm">{{ $errors->first('units') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-center gap-2">
                                {{__('checkout.total')}}: <div class="flex items-center gap-2">
                                    <div class="font-bold text-lg">${{$totalPrice}}</div>
                                    <div class="text-sm font-thin">
                                        {{$discountTier}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <button type="submit"
                                class="w-full max-w-xs text-lg font-semibold shadow-sm rounded-lg py-3 bg-[#EA1F88] text-white hover:bg-[#c01770] focus:outline-none focus:ring-2 focus:ring-[#080B53] transition ease-in duration-200">
                                {{__('checkout.next')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="w-3/4 2xl:w-2/3 mx-auto mt-12">
        <div class="text-center text-xl font-thin">{{__('checkout.save_by_subscribing')}}</div>
        <div class="grid grid-cols-3 gap-4 mt-12">
            @foreach ($products as $product)
            @include('livewire.purchase.pricing-tier', ['product' => $product])
            @endforeach
        </div>
    </div>
    @if($displayCalculator)
    <x-experior::modal>
        @livewire('unit-calculator.unit-calculator')
    </x-experior::modal>
    @endif
</div>
