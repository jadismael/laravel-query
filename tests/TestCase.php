<?php

namespace Tests;

use Jadismael\LaravelQuery\LaravelQueryServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function invokeMethod(object $object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelQueryServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Configure the in-memory SQLite database
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
