<?php

namespace Brunocfalcao\Larapush\Commands;

use sixlive\DotenvEditor\DotenvEditor;
use Brunocfalcao\Larapush\Abstracts\InstallerBootstrap;

/**
 * Larapush local computer installation command.
 * Used to install Larapush in the development computer.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class InstallLocalCommand extends InstallerBootstrap
{
    protected $url;

    protected $messages = [
        'client.required' => 'The --client option is required.',
        'client.integer'  => 'The --client option needs to be an integer.',
        'secret.required' => 'The --secret option is required.',
        'token.required'  => 'The --token option is required.',
    ];

    protected $signature = 'larapush:install-local
                            {--client= : Your OAuth Laravel Passport Client id}
                            {--secret= : Your OAuth Secret}
                            {--token= : The Remote server token, must be the same}';

    protected $description = 'Installs Larapush in your local development computer';

    public function handle()
    {
        parent::handle();

        $this->steps = 4;

        $fields = $this->validateOptions();
        if (! $fields->ok) {
            $this->error($fields->message);

            return;
        }

        $bar = $this->output->createProgressBar($this->steps);
        $bar->start();

        /*
         * In case of re-installation, it should delete the previous
         * Larapush key values so they don't mess up with the configuration.
         */
        $this->bulkInfo(2, 'Cleaning old .env larapush keys (if they exist)...', 1);
        $this->unsetEnvironmentData();
        $bar->advance();

        $this->info('');
        $this->url = $this->askAndValidate(
            'What is your web server url (E.g.: https://www.johnsmith.com) ?',
            'required|url'
        );

        $this->populateEnvironmentData();
        $bar->advance();

        $this->publishLarapushResources();
        $bar->advance();

        $this->clearConfigurationCache();
        $bar->finish();

        $this->allDone();
    }

    protected function populateEnvironmentData()
    {
        $this->bulkInfo(0, 'Setting .env values...', 1);

        $env = new DotenvEditor;
        $env->load(base_path('.env'));
        $env->set('LARAPUSH_TYPE', 'local');
        $env->set('LARAPUSH_TOKEN', $this->option('token'));
        $env->set('LARAPUSH_OAUTH_CLIENT', $this->option('client'));
        $env->set('LARAPUSH_OAUTH_SECRET', $this->option('secret'));
        $env->set('LARAPUSH_REMOTE_URL', $this->url);
        $env->save();
        unset($env);
    }

    protected function allDone()
    {
        $this->bulkInfo(2, 'Remark: Don\'t forget to update your larapush.php configuration file for the correct codebase files and directories that you want to upload.', 1);
        $this->bulkInfo(0, 'All good! Now push something amazing :)', 1);
    }
}
