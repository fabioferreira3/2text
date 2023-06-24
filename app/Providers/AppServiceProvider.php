<?php

namespace App\Providers;

use App\Http\Livewire\Common\Modal;
use App\Jobs\DownloadAudio;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Jobs\DownloadAudio', function ($app) {
            return app(DownloadAudio::class);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::componentNamespace('App\\Http\\Livewire\\Common', 'experior');
    }
}
