<?php

namespace Brunocfalcao\Larapush\Utilities;

use Brunocfalcao\Larapush\Exceptions\AccessTokenException;
use Brunocfalcao\Larapush\Exceptions\LocalException;
use Brunocfalcao\Larapush\Utilities\AccessToken;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpZip\ZipFile;

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
        if (count(app('config')->get('larapush.codebase')) == 0) {
            throw new LocalException('No files or folders identified to upload. Please check your configuration file');
        }

        // Computes the exact file paths that should be included in the codebase zip.
        $codebase = $this->getFileResources(app('config')->get('larapush.codebase'));
        $blacklist = $this->getFileResources(app('config')->get('larapush.blacklist'));

        // Remove the blacklist resources from the codebase resources.
        $codebase = $codebase->reject(function ($resource) use ($blacklist) {
            $exists = false;

            $blacklist->each(function ($item) use (&$exists, $resource) {
                if ($item->realPath() == $resource->realPath()) {
                    $exists = true;
                }
            });

            return $exists;
        })->values();

        $latestFolder = $this->getLatestTransactionFolderName();

        // If exists, open the zip file, and compare with the files we have.
        if ($latestFolder) {
            $latestCodebase = new \PhpZip\ZipFile();

            $latestCodebase->openFile(
                app('config')->get('filesystems.disks.larapush.root').
                '/'.
                $latestFolder.
                '/codebase.zip'
            );

            $zip = $this->getFileResourcesFromZip($latestCodebase);

            if (app('config')->get('larapush.delta_upload') == true) {
                // Remove all the resources that have the same datetime as the zip. Just the modified ones remain + new ones.
                $codebase = $codebase->reject(function ($codebaseResource) use ($zip) {
                    if ($codebaseResource->type() == 'folder') {
                        return false;
                    }

                    $toRemove = false;
                    $zip->each(function ($zipResource) use (&$toRemove, $codebaseResource) {
                        if ($zipResource->relativePath() == $codebaseResource->relativePath()) {
                            if ($zipResource->modifiedDate()->greaterThanOrEqualTo($codebaseResource->modifiedDate()) &&
                            $codebaseResource->type() == 'file') {
                                $toRemove = true;
                            }

                            return false;
                        }
                    });

                    return $toRemove;
                });
            }
        }

        if ($codebase->count() > 0) {
            // Transform codebase resource collection into a glob.
            $codebase->transform(function ($item, $key) {
                return $item->realPath();
            });

            // Create a new transaction folder inside the larapush storage.
            Storage::disk('larapush')->makeDirectory($transaction);

            // Create zip, and store it inside the transaction folder.
            $this->CreateCodebaseZip(larapush_storage_path("{$transaction}/codebase.zip"), $codebase->toArray());

            // Store the runbook, and the zip codebase file.
            Storage::disk('larapush')->put(
                "{$transaction}/runbook.json",
                json_encode(app('config')->get('larapush.scripts'))
            );
        }
    }

    private function getFileResourcesFromZip($ZipFile)
    {
        $zipResources = collect($ZipFile->getAllInfo());
        $resources = collect();
        foreach ($zipResources as $zipInfo) {
            $resources->push(new ZipResource($zipInfo));
        }

        return $resources;
    }

    private function getLatestTransactionFolderName()
    {
        $path = app('config')->get('filesystems.disks.larapush.root');

        $latest_ctime = 0;
        $latest_dir = '';
        $d = dir($path);

        while (false !== ($entry = $d->read())) {
            $filepath = "{$path}/{$entry}";

            if (is_dir($filepath) && filectime($filepath) > $latest_ctime) {
                $latest_ctime = filectime($filepath);
                $latest_dir = $entry;
            }
        } //end loop

        return $latest_dir == '.' ? null : $latest_dir;
    }

    private function getFileResources(array $relativePaths = [])
    {
        $files = collect();

        collect($relativePaths)->each(function ($item) use (&$files) {
            if (is_dir(base_path($item))) {
                $files = $files->merge(glob_recursive(base_path($item.'/*')));
            }

            if (is_file(base_path($item))) {
                $files = $files->merge(glob_recursive(base_path($item)));
            }
        });

        // Transform each item into a FileResource.
        $files->transform(function ($item) {
            return new FileResource($item);
        });

        return $files;
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

    public function CreateCodebaseZip(string $fqfilename, array $glob) : void
    {
        $zipFile = new ZipFile();

        collect($glob)->each(function ($item) use (&$zipFile) {
            if (is_dir($item)) {
                $zipFile->addEmptyDir($item);
            }

            if (is_file($item)) {
                $zipFile->addFile($item, substr($item, strlen(base_path()) + 1));
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
        if (! $response->isOk || data_get($response->payload, 'error') != null) {
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
