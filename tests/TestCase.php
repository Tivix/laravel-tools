<?php

namespace Kellton\Tools\Tests;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Foundation\Application;
use Kellton\Tools\ToolsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase is the base class for all test cases.
 */
class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ToolsServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     *
     * @return void
     */
    public function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Returns the faker instance.
     *
     * @return Generator
     */
    public function faker(): Generator
    {
        return FakerFactory::create();
    }
}
