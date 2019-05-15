<?php

namespace Brunocfalcao\Larapush;

use Brunocfalcao\Larapush\Commands\InstallLocalCommand;
use Brunocfalcao\Larapush\Commands\InstallRemoteCommand;
use Brunocfalcao\Larapush\Commands\PushCommand;
use Brunocfalcao\Larapush\Commands\TestCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Laravel\Passport\Passport;

final class LarapushServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfiguration();

        if (config('larapush.type') == 'remote' && class_exists('\Laravel\Passport\Passport')) {
            $this->loadRemoteRoutes();
        }

        $this->registerStorage();
    }

    private function registerStorage()
    {
        $this->app['config']->set('filesystems.disks', [
            'larapush' => [
                'driver' => 'local',
                'root' => app('config')->get('larapush.storage.path'),
            ],
        ]);
    }

    protected function publishConfiguration()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../configuration/larapush.php' => config_path('larapush.php'),
            ], 'larapush-configuration');
        }
    }

    protected function loadRemoteRoutes()
    {
        // Load Larapush routes using the api middleware.
        Route::as('larapush.')
             ->middleware('same-token', 'client')
             ->namespace('Brunocfalcao\Larapush\Http\Controllers')
             ->prefix(app('config')->get('larapush.remote.prefix'))
             ->group(__DIR__.'/../routes/api.php');

        // Load Laravel Passport routes.
        Passport::routes();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../configuration/larapush.php',
            'larapush'
        );

        $this->commands([
            InstallRemoteCommand::class,
            InstallLocalCommand::class,
            PushCommand::class,
            TestCommand::class
        ]);

        app('router')->aliasMiddleware(
            'client',
            CheckClientCredentials::class
        );

        app('router')->aliasMiddleware(
            'is-json',
            \Brunocfalcao\Larapush\Middleware\IsJson::class
        );

        app('router')->aliasMiddleware(
            'same-token',
            \Brunocfalcao\Larapush\Middleware\SameToken::class
        );
    }
}
