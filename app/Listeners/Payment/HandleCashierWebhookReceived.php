<?php

namespace App\Listeners\Payment;

use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived as CashierWebhookReceived;

class HandleCashierWebhookReceived
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
            Log::debug($event->payload);
        }
    }
}
