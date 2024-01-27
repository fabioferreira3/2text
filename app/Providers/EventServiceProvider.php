<?php

namespace App\Providers;

use App\Events\DocumentTaskFinished;
use App\Events\UserCreated;
use App\Listeners\HandleDocumentTasksCompletedUpdate;
use App\Listeners\HandleNewUserAdminNotification;
use App\Listeners\HandleWelcomeNotification;
use App\Listeners\Payment\HandleCashierWebhookReceived;
use App\Listeners\Payment\HandlePaymentSucceeded;
use Laravel\Cashier\Events\WebhookReceived as CashierWebhookReceived;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        DocumentTaskFinished::class => [
            HandleDocumentTasksCompletedUpdate::class
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserCreated::class => [
            HandleNewUserAdminNotification::class,
            HandleWelcomeNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            \SocialiteProviders\Google\GoogleExtendSocialite::class . '@handle',
            \SocialiteProviders\Apple\AppleExtendSocialite::class . '@handle',
            \SocialiteProviders\LinkedIn\LinkedInExtendSocialite::class . '@handle',
            \SocialiteProviders\Medium\MediumExtendSocialite::class . '@handle',
        ],
        CashierWebhookReceived::class => [
            HandleCashierWebhookReceived::class,
        ],
        \Spark\Events\PaymentSucceeded::class => [
            HandlePaymentSucceeded::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
