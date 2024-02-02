<?php

namespace App\Listeners\Payment;

use App\Exceptions\Payment\PaymentFailedException;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Spark\Events\PaymentSucceeded;

class HandlePaymentSucceeded
{
    /**
     * Handle the event.
     *
     * @param PaymentSucceeded $event
     * @return void
     */
    public function handle(PaymentSucceeded $event)
    {
        try {
            $invoiceId = $event->invoice->id;
            $productId = $event->invoice->lines['data'][0]['price']['product'];

            $product = Product::ofExternalId($productId)->firstOrFail();
            $quantity = $event->invoice->lines['data'][0]['quantity'];

            if ($product->meta['unit_credits'] ?? false) {
                $quantity = $product->meta['unit_credits'];
            }

            $event->billable->account->addUnits($quantity, [
                'invoice_id' => $invoiceId,
            ]);
        } catch (Exception $e) {
            Log::error($event->invoice);
            throw new PaymentFailedException($e->getMessage());
        }
    }
}
