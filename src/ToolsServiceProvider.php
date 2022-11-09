<?php

namespace Kellton\Tools;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Kellton\Tools\Feature\Data\Data;
use Kellton\Tools\Feature\Data\Services\DefinitionService;
use Kellton\Tools\Feature\Data\Services\PropertyService;

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

        // Set definition service as singleton
        $this->app->singleton(
            DefinitionService::class,
            fn () => new DefinitionService(app(PropertyService::class))
        );

        // Resolve the data class
        $this->app->beforeResolving(Data::class, function ($class, $parameters, $app) {
            if ($app->has($class)) {
                return;
            }

            $app->bind(
                $class,
                fn ($container) => $class::create($container['request'])
            );
        });
    }
}
