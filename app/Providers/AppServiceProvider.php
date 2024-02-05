<?php

namespace App\Providers;

use App\Factories\AssemblyAIFactory;
use App\Factories\ChatGPTFactory;
use App\Factories\OraculumFactory;
use App\Factories\WhisperFactory;
use App\Interfaces\AssemblyAIFactoryInterface;
use App\Interfaces\ChatGPTFactoryInterface;
use App\Interfaces\OraculumFactoryInterface;
use App\Interfaces\WhisperFactoryInterface;
use App\Jobs\DownloadAudio;
use App\View\Components\Custom\Dropdown;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;

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
        $this->app->bind(WhisperFactoryInterface::class, WhisperFactory::class);
        $this->app->bind(AssemblyAIFactoryInterface::class, AssemblyAIFactory::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::componentNamespace('App\\Livewire\\Common', 'experior');
        Blade::component('custom.dropdown', Dropdown::class);

        Queue::failing(function (JobFailed $event) {
            //    $event->job->jobFailed($event->exception->getMessage());
        });
    }
}
