<?php

namespace Laravel\JsonObject\Tests;

use Laravel\JsonObject\JsonServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            JsonServiceProvider::class,
        ];
    }
}
