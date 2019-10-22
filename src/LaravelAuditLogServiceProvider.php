<?php

namespace NeelBhanushali\LaravelAuditLog;

use Illuminate\Support\ServiceProvider;

class LaravelAuditLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations')
        ], 'laravel-audit-log.migrations');
    }
}
