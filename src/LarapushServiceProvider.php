<?php

namespace Brunocfalcao\Larapush;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Brunocfalcao\Larapush\Commands\PushCommand;
use Brunocfalcao\Larapush\Commands\InstallLocalCommand;
use Brunocfalcao\Larapush\Commands\InstallRemoteCommand;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

/**
 * Class that bootstraps Larapush service provider.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class LarapushServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfiguration();

        if (config('larapush.type') === 'remote' && class_exists('\Laravel\Passport\Passport')) {
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
             //->namespace('Brunocfalcao\Larapush\Http\Controllers')
             ->prefix(app('config')->get('larapush.remote.suffix'))
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
