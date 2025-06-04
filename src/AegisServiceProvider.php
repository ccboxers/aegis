<?php

namespace Layman\Aegis;

use Illuminate\Support\ServiceProvider;
use Layman\Aegis\Console\AegisCommand;
use Layman\Aegis\Support\Aegis;

class AegisServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('aegis', function () {
            return new Aegis();
        });
        $this->mergeConfigFrom(
            __DIR__ . '/../config/aegis.php', 'aegis'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AegisCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/aegis.php' => config_path('aegis.php'),
            ], 'config');
        }
    }
}
