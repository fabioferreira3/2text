<div class="container mx-auto px-4">
    <div class="bg-white p-6 mt-12">
        <div class="flex flex-col items-center">
            <svg class="w-16 h-16 text-green-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Payment Successful!</h2>
            <p class="text-gray-600">Thank you for your purchase.</p>

            <div class="mt-4">
                <p><strong>Quantity:</strong> {{ $quantity }} units</p>
                <p><strong>Total Amount:</strong> ${{$totalAmount}}</p>
            </div>

            <a href="{{ route('home') }}" class="mt-6 inline-block bg-secondary text-white py-2 px-4 rounded">
                Back to dashboard
            </a>
        </div>
    </div>
</div>