<?php

namespace MetaSeo\Tests;

use MetaSeo\Facade\Meta;
use MetaSeo\MetaSeoServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MetaSeoServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Meta' => Meta::class,
        ];
    }
}
