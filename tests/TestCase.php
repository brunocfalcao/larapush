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
        $app->setBasePath(__DIR__);

        $app['config']->set(
            'larapush.storage.path',
            base_path('disk')
        );

        $app['config']->set(
            'larapush.codebase',
            ['codebase']
        );

        $app['config']->set('filesystems.disks', [
            'larapush' => [
                'driver' => 'local',
                'root' => $app['config']->get('larapush.storage.path'),
            ],
        ]);
    }
}
