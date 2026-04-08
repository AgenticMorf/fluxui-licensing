<?php

namespace AgenticMorf\FluxUILicensing\Tests;

use AgenticMorf\FluxUILicensing\FluxUILicensingServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FluxUILicensingServiceProvider::class,
        ];
    }
}
