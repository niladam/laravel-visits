<?php

namespace Niladam\LaravelVisits;

use Illuminate\Support\ServiceProvider;

class LaravelVisitsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        /*
         * Optional methods to load your package assets
         */
         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-visits.php' => config_path('laravel-visits.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_visits_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_visits_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-visits.php', 'laravel-visits');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-visits', function () {
            return new LaravelVisits;
        });
    }
}
