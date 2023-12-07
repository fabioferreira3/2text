<?php

namespace App\Providers;

use App\Factories\ChatGPTFactory;
use App\Factories\OraculumFactory;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Jobs\DownloadAudio;
use App\Models\Account;
use App\View\Components\Custom\Dropdown;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

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
        $this->app->bind(OraculumFactoryInterface::class, OraculumFactory::class);
        $this->app->bind(ChatGPTFactoryInterface::class, ChatGPTFactory::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::componentNamespace('App\\Http\\Livewire\\Common', 'experior');
        Blade::component('custom.dropdown', Dropdown::class);
        Cashier::useCustomerModel(Account::class);
    }
}
