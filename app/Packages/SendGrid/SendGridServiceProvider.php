<?php

namespace App\Packages\SendGrid;

use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class SendGridServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('sendgrid', function ($app) {
            return new SendGrid();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('sendgrid.php'),
        ]);
    }
}
