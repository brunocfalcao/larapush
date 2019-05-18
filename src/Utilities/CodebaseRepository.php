<?php

namespace Brunocfalcao\Larapush\Utilities;

use PhpZip\ZipFile;
use Illuminate\Support\Facades\Storage;
use Brunocfalcao\Larapush\Exceptions\LocalException;

/**
 * Class that stores a codebase repository structure.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class CodebaseRepository
{
    private $runbook;
    private $codebaseStream;
    private $transaction;

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
        $directory = dir($path);

        while (($entry = $directory->read()) !== false) {
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

        if (app('config')->get('larapush.delta_upload') == true) {
            $latestFolder = $this->getLatestTransactionFolderName();

            // If exists, open the zip file, and compare with the files we have.
            if ($latestFolder) {
                $latestCodebase = new ZipFile;

                $latestCodebase->openFile(
                    app('config')->get('filesystems.disks.larapush.root').
                    '/'.
                    $latestFolder.
                    '/codebase.zip'
                );

                $zip = $this->getFileResourcesFromZip($latestCodebase);

                // Remove all the resources that have the same datetime as the zip. Just the modified ones remain + new ones.
                $codebase = $codebase->reject(function ($codebaseResource) use ($zip) {
                    if ($codebaseResource->type() == 'folder') {
                        return false;
                    }

                    $toRemove = false;
                    $zip->each(function ($zipResource) use (&$toRemove, $codebaseResource) {
                        if ($zipResource->relativePath() == $codebaseResource->relativePath()) {
                            if ($zipResource->modifiedDate()->greaterThanOrEqualTo($codebaseResource->modifiedDate()) &&
                                $codebaseResource->type() === 'file') {
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
            $codebase->transform(function ($item) {
                return $item->realPath();
            });

            // Create a new transaction folder inside the larapush storage.
            Storage::disk('larapush')->makeDirectory($transaction);

            // Create zip, and store it inside the transaction folder.
            $this->createCodebaseZip(larapush_storage_path("{$transaction}/codebase.zip"), $codebase->toArray());

            // Store the runbook, and the zip codebase file.
            Storage::disk('larapush')->put(
                "{$transaction}/runbook.json",
                json_encode(app('config')->get('larapush.scripts'))
            );
        }
    }

    public function createCodebaseZip(string $fqfilename, array $glob) : void
    {
        $zipFile = new ZipFile;

        $zipFile->setCompressionLevel(9);

        collect($glob)->each(function ($item) use (&$zipFile) {
            if (is_dir($item)) {
                $zipFile->addEmptyDir(substr($item, strlen(base_path()) + 1), $item);
            }

            if (is_file($item)) {
                $zipFile->addFile($item, substr($item, strlen(base_path()) + 1));
            }
        });

        $zipFile->saveAsFile($fqfilename);
        $zipFile->close();
    }

    public function withCodebaseStream(string $codebaseStream)
    {
        $this->codebaseStream = $codebaseStream;

        return $this;
    }

    public function withTransaction(string $transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function withRunbook(string $runbook)
    {
        $this->runbook = $runbook;

        return $this;
    }

    public function codebaseStream()
    {
        return $this->codebaseStream;
    }

    public function runbook()
    {
        return $this->runbook;
    }

    public function transaction()
    {
        return $this->transaction;
    }
}
