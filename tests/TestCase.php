<?php

namespace Obelaw\Basketin\Cart\Tests;

use Obelaw\Basketin\Cart\Providers\BasketinCartServiceProvider;
use Obelaw\Basketin\Cart\Tests\App\Providers\BasketinCartTestServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;


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
            BasketinCartServiceProvider::class,
            BasketinCartTestServiceProvider::class,
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
        $app['config']->set('basketin.cart.setup.auto_migrate', true);
    }
}
