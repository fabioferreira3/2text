<div class="container mx-auto px-4">
    <div class="bg-white p-6 mt-12">
        <div class="flex flex-col items-center">
            <svg class="w-16 h-16 text-green-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">{{__('checkout.payment_successfull')}}</h2>
            <div class="flex flex-col items-center justify-center text-gray-600">
                <p>{{__('checkout.thank_you')}}</p>
                <p>{{__('checkout.your_unit_balance')}}</p>
            </div>

            <div class="mt-4 p-4 border border-gray-200 rounded-xl">
                <p><strong>{{__('checkout.quantity')}}:</strong> {{ $quantity }} {{__('checkout.units')}}</p>
                <p><strong>{{__('checkout.total_amount')}}:</strong> ${{$totalAmount}}</p>
            </div>

            <a href="{{ route('home') }}" class="mt-6 inline-block bg-secondary text-white py-2 px-4 rounded-lg">
                {{__('checkout.back_dashboard')}}
            </a>
        </div>
    </div>
</div>
