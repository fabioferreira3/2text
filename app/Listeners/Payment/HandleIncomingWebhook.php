<?php

namespace App\Listeners\Payment;

use Laravel\Cashier\Events\WebhookReceived as CashierWebhookReceived;

class HandleIncomingWebhook
{
    /**
     * Handle the event.
     *
     * @param CashierWebhookReceived $event
     * @return void
     */
    public function handle(CashierWebhookReceived $event)
    {
    }
}
