<?php

namespace Kellton\Tools;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

/**
 * Class ToolsServiceProvider handles the registration of the tools package.
 */
class ToolsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('tools.php'),
            ], 'config');
        }

        // Register macro for casting
        Collection::macro('recursive', function () {
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->map(function ($value) {
                if (is_array($value) || $value instanceof Collection) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'tools');
    }
}
