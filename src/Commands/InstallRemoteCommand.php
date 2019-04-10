<?php

namespace Brunocfalcao\Larapush\Commands;

use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;
use sixlive\DotenvEditor\DotenvEditor;
use Illuminate\Support\Facades\Artisan;
use Brunocfalcao\Larapush\Abstracts\InstallerBootstrap;

final class InstallRemoteCommand extends InstallerBootstrap
{
    private $client;

    private $secret;

    protected $signature = 'larapush:install-remote';

    protected $description = 'Installs Larapush on your remote environment';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        parent::handle();

        $this->steps = 5;

        // Laravel Passport installed?
        if (! is_dir(base_path('vendor/laravel/passport'))) {
            $this->steps += 5;
        }

        $this->bulkInfo(2, 'Installing Larapush on a REMOTE environment...', 1);
        $this->bar = $this->output->createProgressBar($this->steps);
        $this->bar->start();

        // In case of a re-installation, delete all the .env larapush data.
        $this->bulkInfo(2, 'Cleaning old .env larapush keys (if they exist)...', 1);
        $this->unsetEnvData();
        $this->bar->advance();

        if (! is_dir(base_path('vendor/laravel/passport')) && ! class_exists('\Laravel\Passport\Passport')) {
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
        $this->bulkInfo(2, 'ALL DONE!', 0);
        $this->bulkInfo(1, 'Please install Larapush on your local Laravel app and run the following artisan command:', 1);
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
            //Artisan::call("passport:install");
            $this->runProcess('php artisan passport:install');
        }, function ($exception) {
            $this->exception = $exception;
            $this->gracefullyExit();
        });

        //$this->runProcess('php artisan passport:install');
        $this->bar->advance();

        $this->bulkInfo(2, 'Dumping autoload...', 1);
        $this->bar->advance();
        $this->runProcess('composer dumpautoload');
    }
}
