<?php

namespace MetaSeo;

use Illuminate\Support\ServiceProvider;

class MetaSeoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MetaManager::class, function ($app) {
            return new MetaManager();
        });

        $this->app->alias(MetaManager::class, 'meta-seo');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
