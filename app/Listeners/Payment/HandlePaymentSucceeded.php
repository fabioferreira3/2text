<?php

namespace App\Listeners\Payment;

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
        Log::debug($event->billable->id);
    }
}
