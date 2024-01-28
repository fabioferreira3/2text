<?php

namespace App\Listeners\Payment;

use App\Exceptions\PaymentSucceededException;
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
            $productId = $event->invoice->lines['data'][0]['plan']['product'];

            $product = Product::ofExternalId($productId)->firstOrFail();

            if ($product->meta['unit_credits'] ?? false) {
                $event->billable->account->addUnits($product->meta['unit_credits'], [
                    'invoice_id' => $invoiceId,
                ]);
            }
        } catch (Exception $e) {
            Log::error($event->invoice);
            throw new PaymentSucceededException($e->getMessage());
        }
    }
}
