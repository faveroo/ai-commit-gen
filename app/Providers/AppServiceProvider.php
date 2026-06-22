<?php

namespace App\Providers;

use App\AI\Contracts\AIProvider;
use App\Factories\AIProviderFactory;
use App\Repositories\ConfigRepository;
use App\Services\AiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ConfigRepository::class);

        $this->app->singleton(AIProvider::class, function ($app) {
            return $app->make(AIProviderFactory::class)->make();
        });

        $this->app->singleton(AiService::class, function ($app) {
            return new AiService($app->make(AIProvider::class));
        });
    }
}
