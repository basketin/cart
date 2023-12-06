<?php

namespace Storephp\Cart\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Storephp\Cart\Providers\StorePHPCartServiceProvider;
use Storephp\Cart\Tests\App\Providers\StorePHPCartTestServiceProvider;


class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh', [
            '--database' => 'testing',
        ]);

        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            StorePHPCartServiceProvider::class,
            StorePHPCartTestServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBbBTsmF');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
