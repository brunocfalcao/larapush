<?php

namespace Brunocfalcao\Larapush\Commands;

use PhpZip\ZipFile;
use Brunocfalcao\Larapush\Exceptions\LocalException;
use Brunocfalcao\Larapush\Abstracts\InstallerBootstrap;
use Brunocfalcao\Larapush\Concerns\SimplifiesConsoleOutput;

final class TestCommand extends InstallerBootstrap
{
    use SimplifiesConsoleOutput;

    protected $signature = 'test';

    protected $description = 'Command for testing purposes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        parent::handle();

        /*
         * Load a package of files.
         */

        if (count(app('config')->get('larapush.codebase')) == 0) {
            throw new LocalException('No files or folders identified to upload. Please check your configuration file');
        }

        $zipFile = new ZipFile();

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

            /** SelectionType::CHANGED */

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

                if ($toRemove) {
                    $this->info('Removing '.$codebaseResource->relativePath());
                }

                return $toRemove;
            });
        }

        if ($codebase->count() > 0) {
            // Transform codebase resource collection into a glob.
            $codebase->transform(function ($item, $key) {
                return $item->realPath();
            });
        }
    }

    public function getLatestTransactionFolderName()
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

        return $latest_dir;
    }

    /**
     * Generates a FileResource for each of the files detected via the relativePaths.
     *
     * @param  array  $relativePaths
     * @return array
     */
    public function getFileResources(array $relativePaths = [])
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

    public function getFileResourcesFromZip($ZipFile)
    {
        $zipResources = collect($ZipFile->getAllInfo());
        $resources = collect();
        foreach ($zipResources as $zipInfo) {
            $resources->push(new ZipResource($zipInfo));
        }

        return $resources;
    }
}
