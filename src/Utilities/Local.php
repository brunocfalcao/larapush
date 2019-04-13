<?php

namespace Brunocfalcao\Larapush\Utilities;

use PhpZip\ZipFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Brunocfalcao\Larapush\Exceptions\LocalException;
use Brunocfalcao\Larapush\Exceptions\AccessTokenException;

final class Local
{
    public static function __callStatic($method, $args)
    {
        return LocalOperation::new()->{$method}(...$args);
    }
}

final class LocalOperation
{
    private $accessToken;

    protected $zipFilename;

    public function askRemoteToCheckEnvironment()
    {
        $response = ReSTCaller::asPost()
                          ->withHeader('Authorization', 'Bearer '.$this->accessToken->token)
                          ->withHeader('Accept', 'application/json')
                          ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                          ->withPayload(['environments' => implode(',', app('config')->get('larapush.environment.reserved'))])
                          ->call(larapush_remote_url('check-environment'));

        $this->checkResponseStatus($response);

        return (bool) $response->payload['prompt'];
    }

    public function createRepository(string $transaction) : void
    {
        // Create a new transaction folder inside the larapush storage.
        Storage::disk('larapush')->makeDirectory($transaction);

        // Create zip, and store it inside the transaction folder.
        $this->CreateCodebaseZip(larapush_storage_path("{$transaction}/codebase.zip"));

        // Store the runbook, and the zip codebase file.
        Storage::disk('larapush')->put(
            "{$transaction}/runbook.json",
            json_encode(app('config')->get('larapush.scripts'))
        );
    }

    public function runPostScripts(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                              ->withHeader('Authorization', 'Bearer '.$this->accessToken->token)
                              ->withHeader('Accept', 'application/json')
                              ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                              ->withPayload(['transaction' => $transaction])
                              ->call(larapush_remote_url('post-scripts'));

        $this->checkResponseStatus($response);
    }

    public function deploy(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                              ->withHeader('Authorization', 'Bearer '.$this->accessToken->token)
                              ->withHeader('Accept', 'application/json')
                              ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                              ->withPayload(['transaction' => $transaction])
                              ->call(larapush_remote_url('deploy'));

        $this->checkResponseStatus($response);
    }

    public function runPreScripts(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                              ->withHeader('Authorization', 'Bearer '.$this->accessToken->token)
                              ->withHeader('Accept', 'application/json')
                              ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                              ->withPayload(['transaction' => $transaction])
                              ->call(larapush_remote_url('pre-scripts'));

        $this->checkResponseStatus($response);
    }

    public function CreateCodebaseZip(string $fqfilename) : void
    {
        if (count(app('config')->get('larapush.codebase')) == 0) {
            throw new LocalException('No files or folders identified to upload. Please check your configuration file');
        }

        $zipFile = new ZipFile();

        $startIdx = strlen(base_path()) + 1;

        collect(app('config')->get('larapush.codebase'))->each(function ($item) use (&$zipFile) {

            if (is_dir(base_path($item))) {
                $files = glob_recursive(base_path($item.'/*'));
            }

            if (is_file(base_path($item))) {
                $files = glob_recursive(base_path($item));
            }

            foreach ($files as $file) {
                $unixPath = str_replace(DIRECTORY_SEPARATOR, '/', substr($file, strlen(base_path()) + 1));
                $unixBasePath = str_replace(DIRECTORY_SEPARATOR, '/', base_path($unixPath));

                if (is_dir(base_path($unixPath))) {
                    $zipFile->addEmptyDir($unixPath);
                }

                if (is_file(base_path($unixPath))) {
                    $zipFile->addFile($unixBasePath, $unixPath);
                }
            }
        });

        $zipFile->saveAsFile($fqfilename);

        $zipFile->close();
    }

    public function uploadCodebase(string $transaction) : void
    {
        $response = ReSTCaller::asPost()
                              ->withHeader('Authorization', 'Bearer '.$this->accessToken->token)
                              ->withHeader('Accept', 'application/json')
                              ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                              ->withPayload(['transaction' => $transaction])
                              ->withPayload(['runbook' => json_encode(app('config')->get('larapush.scripts'))])
                              ->withPayload(['codebase' => base64_encode(file_get_contents(larapush_storage_path("{$transaction}/codebase.zip")))])
                              ->call(larapush_remote_url('upload'));

        $this->checkResponseStatus($response);
    }

    public function preChecks() : void
    {
        $storagePath = app('config')->get('larapush.storage.path');
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        if (! is_writable($storagePath)) {
            throw new LocalException('Local storage directory not writeable');
        }
    }

    public function getAccessToken()
    {
        $response = ReSTCaller::asPost()
                           ->withPayload(['grant_type'    => 'client_credentials',
                                          'client_id'     => app('config')->get('larapush.oauth.client'),
                                          'client_secret' => app('config')->get('larapush.oauth.secret'), ])
                           ->withHeader('Accept', 'application/json')
                           ->call(app('config')->get('larapush.remote.url').'/oauth/token');

        $this->checkAccessToken($response);

        $this->accessToken = new AccessToken(
            $response->payload['expires_in'],
            $response->payload['access_token']
        );

        return $this;
    }

    public function askRemoteForPreChecks() : void
    {
        $response = ReSTCaller::asPost()
                          ->withHeader('Authorization', 'Bearer '.$this->accessToken->token)
                          ->withHeader('Accept', 'application/json')
                          ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                          ->call(larapush_remote_url('prechecks'));

        $this->checkResponseStatus($response);
    }

    public function ping() : void
    {
        $response = ReSTCaller::asPost()
                          ->withHeader('Authorization', 'Bearer '.$this->accessToken->token)
                          ->withHeader('Accept', 'application/json')
                          ->withPayload(['larapush-token' => app('config')->get('larapush.token')])
                          ->call(larapush_remote_url('ping'));

        $this->checkResponseStatus($response);
    }

    private function checkResponseStatus(ResponsePayload $response) : void
    {
        if (! $response->isOk) {
            throw new LocalException(get_response_payload_friendly_message($response));
        }
    }

    private function checkAccessToken(?ResponsePayload $response) : void
    {
        if (! $response->isOk || data_get($response->payload, 'access_token') == null) {
            throw new AccessTokenException(get_response_payload_friendly_message($response));
        }
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }
}

class AccessToken
{
    public $expiresIn = null;
    public $token = null;

    public function __construct(int $expiresIn, string $token)
    {
        list($this->expiresIn, $this->token) = [$expiresIn, $token];
    }
}
