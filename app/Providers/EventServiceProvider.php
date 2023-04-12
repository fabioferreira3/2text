<?php

namespace App\Providers;

use App\Events\DocumentTaskFailed;
use App\Events\DocumentTaskFinished;
use App\Events\FailedTextRequest;
use App\Listeners\HandleFailedDocumentTask;
use App\Listeners\HandleFailedTextRequest;
use App\Listeners\HandleFinishedDocumentTask;
use App\Models\TextRequest;
use App\Models\TextRequestLog;
use App\Observers\TextRequestLogObserver;
use App\Observers\TextRequestObserver;
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
        FailedTextRequest::class => [
            HandleFailedTextRequest::class
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DocumentTaskFinished::class => [
            HandleFinishedDocumentTask::class
        ],
        DocumentTaskFailed::class => [
            HandleFailedDocumentTask::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        TextRequest::observe(TextRequestObserver::class);
        TextRequestLog::observe(TextRequestLogObserver::class);
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
