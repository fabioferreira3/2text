<div class="flex flex-col justify-center items-center h-screen">
    <div class="bg-white shadow-lg rounded-lg p-8 w-2/3">
        <div class="mb-4">
            <label for="card-holder-name" class="block text-gray-700 text-sm font-bold mb-2">Card Holder's Name</label>
            <input id="card-holder-name"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                type="text" placeholder="Name on card">
        </div>

        <!-- Stripe Elements Placeholder -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Card Details</label>
            <div id="card-element"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>

        <button id="card-button"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Process Payment
        </button>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>

<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');

    const elements = stripe.elements();
    const cardElement = elements.create('card');

    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');

    cardButton.addEventListener('click', async (e) => {
    const { paymentMethod, error } = await stripe.createPaymentMethod(
    'card', cardElement, {
    billing_details: { name: cardHolderName.value }
    }
    );

    if (error) {
    // Display "error.message" to the user...
    } else {
        window.location.href = `http://localhost/charge?pmid=${paymentMethod.id}`;
        console.log(paymentMethod.id);
    // The card has been verified successfully...
    }
    });
</script>
@endpush
