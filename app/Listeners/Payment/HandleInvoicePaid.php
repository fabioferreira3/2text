<?php

namespace App\Listeners\Payment;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived as CashierWebhookReceived;

class HandleInvoicePaid
{
    /**
     * Handle the event.
     *
     * @param CashierWebhookReceived $event
     * @return void
     */
    public function handle(CashierWebhookReceived $event)
    {
        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            // $invoiceId = $event->payload['data']['object']['id'];
            // $stripeId = $event->payload['data']['object']['customer'];
            // $productId = $event->payload['data']['object']['lines']['data'][0]['plan']['product'];

            // $user = User::ofStripeId($stripeId)->firstOrFail();
            // $product = Product::ofExternalId($productId)->firstOrFail();

            // if ($product->meta['units_credits'] ?? false) {
            //     $user->account->addUnits($product->meta['units_credit'], [
            //         'invoice_id' => $invoiceId,
            //     ]);
            // }
        }
    }
}
