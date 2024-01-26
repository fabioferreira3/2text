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
        Log::debug($event->payload);
    }
}
