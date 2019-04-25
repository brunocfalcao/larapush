<?php

namespace Brunocfalcao\Larapush\Tests;

use Brunocfalcao\Larapush\LarapushServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LarapushServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        //$app['config']->set('auth.providers.users.model', User::class);
    }
}
