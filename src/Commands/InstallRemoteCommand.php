<?php

namespace Brunocfalcao\Larapush\Commands;

use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;
use sixlive\DotenvEditor\DotenvEditor;
use Illuminate\Support\Facades\Artisan;
use Brunocfalcao\Larapush\Abstracts\InstallerBootstrap;

/**
 * Larapush web server installation command.
 * Used to install Larapush in your web server.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class InstallRemoteCommand extends InstallerBootstrap
{
    private $client;

    private $secret;

    protected $signature = 'larapush:install-remote';

    protected $description = 'Installs Larapush on your web server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        parent::handle();

        $this->steps = 5;

        /*
         * Laravel Passport is a key requirement for Larapush to work.
         * Therefore without having it installed in your web server this
         * package cannot work correctly.
         * Laravel Passport is installed automatically, without any
         * need for your manual configuration.
         */
        if (! $this->passportExists()) {
            $this->steps += 5;
        }

        $this->bulkInfo(2, 'Starting installation on your web server...', 1);
        $this->bar = $this->output->createProgressBar($this->steps);
        $this->bar->start();

        /*
         * In case of a re-installation the old .env keys should be deleted.
         */
        $this->bulkInfo(2, 'Cleaning old .env larapush keys (if they exist)...', 1);
        $this->unsetEnvironmentData();
        $this->bar->advance();

        if (! $this->passportExists()) {
            $this->installLaravelPassport();
        }

        $this->publishLarapushResources();
        $this->bar->advance();

        $this->installClientCredentialsGrant();
        $this->getClientCredentialsGrant();
        $this->bar->advance();

        $this->registerEnvKeys();
        $this->bar->advance();

        $this->clearConfigurationCache();
        $this->bar->finish();

        $this->showLocalInstallInformation();
    }

    protected function passportExists()
    {
        return is_dir(base_path('vendor/laravel/passport')) && class_exists('\Laravel\Passport\Passport');
    }

    protected function registerEnvKeys()
    {
        $this->bulkInfo(2, 'Registering .env keys...', 1);

        $editor = new DotenvEditor;
        $editor->load(base_path('.env'));
        $editor->set('LARAPUSH_TYPE', 'remote');
        $editor->set('LARAPUSH_OAUTH_CLIENT', $this->client);
        $editor->set('LARAPUSH_OAUTH_SECRET', $this->secret);

        $this->token = strtoupper(str_random(10));

        $editor->set('LARAPUSH_TOKEN', $this->token);
        $editor->save();
    }

    protected function showLocalInstallInformation()
    {
        $this->bulkInfo(2, 'All done!', 0);
        $this->bulkInfo(1, 'Please COPY + PASTE the following artisan command on your local computer to install Larapush:', 1);
        $this->info("php artisan larapush:install-local --client={$this->client} --secret={$this->secret} --token={$this->token}");
    }

    protected function getClientCredentialsGrant()
    {
        $client = DB::table('oauth_clients')->latest()->first();
        $this->client = $client->id;
        $this->secret = $client->secret;
    }

    protected function installClientCredentialsGrant()
    {
        $this->bulkInfo(2, 'Installing Laravel Password client credentials grant...', 1);
        $appName = 'Larapush Grant Client';

        larapush_rescue(function () use ($appName) {
            $this->runProcess("php artisan passport:client --client --name=\"{$appName}\" --quiet", getcwd());
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });
    }

    protected function installLaravelPassport()
    {
        $this->bulkInfo(2, 'Requiring Laravel Passport package via Composer (might take some minutes)...', 1);
        $this->bar->advance();

        $this->runProcess('composer require laravel/passport');
        $this->runProcess('composer dumpautoload');

        $this->bulkInfo(2, 'Publishing Laravel Passport configuration...', 1);

        larapush_rescue(function () {
            $this->runProcess('php artisan vendor:publish --tag=passport-config');
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });

        $this->bar->advance();

        $this->bulkInfo(2, 'Running Laravel Passport migrations...', 1);

        larapush_rescue(function () {
            $this->runProcess('php artisan migrate');
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });

        $this->bar->advance();

        $this->bulkInfo(2, 'Installing Laravel Passport...', 1);

        larapush_rescue(function () {
            $this->runProcess('php artisan passport:install');
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });

        $this->bar->advance();

        $this->bulkInfo(2, 'Running Composer dump autoload...', 1);
        $this->bar->advance();
        $this->runProcess('composer dumpautoload');
    }
}
