<?php

namespace Brunocfalcao\Larapush\Commands;

use Brunocfalcao\Larapush\Abstracts\InstallerBootstrap;
use Brunocfalcao\Larapush\Concerns\SimplifiesConsoleOutput;
use Brunocfalcao\Larapush\Exceptions\LocalException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use PhpZip\Model\ZipInfo;
use PhpZip\ZipFile;

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

        $this->info('starting...');

        if (count(app('config')->get('larapush.codebase')) == 0) {
            throw new LocalException('No files or folders identified to upload. Please check your configuration file');
        }

        $zipFile = new ZipFile();

        $this->info('creating codebase resources...');
        $codebase = $this->getFileResources(app('config')->get('larapush.codebase'));

        $this->info('creating blacklist resources...');
        $blacklist = $this->getFileResources(app('config')->get('larapush.blacklist'));

        $this->info('removing blacklist resources...');
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

        $this->info('getting last transaction folder...');
        $latestFolder = $this->getLatestTransactionFolderName();

        $this->info('Latest codebase folder - ' . $latestFolder);

        // If exists, open the zip file, and compare with the files we have.
        if ($latestFolder) {
            $latestCodebase = new \PhpZip\ZipFile();
            $latestCodebase->openFile(app('config')->get('filesystems.disks.larapush.root').'/'.$latestFolder.'/codebase.zip');

            $zip = $this->getFileResourcesFromZip($latestCodebase);

            /*
            $zipResources = collect($latestCodebase->getAllInfo());

            dd($zipResources);

            /** SelectionType::CHANGED */

            $this->info('');
            $this->info('Codebase resources count: ' . $codebase->values()->count());

            // Remove all the resources that have the same datetime as the zip. Just the modified ones remain + new ones.
            $codebase = $codebase->reject(function ($codebaseResource) use ($zip) {

                if ($codebaseResource->type() == 'folder') {
                    return false;
                }

                $this->info('CHECKING ' . $codebaseResource->relativePath());
                $toRemove = false;
                $zip->each(function ($zipResource) use (&$toRemove, $codebaseResource) {

                    if ($zipResource->relativePath() == $codebaseResource->relativePath()) {
                        $this->info($zipResource->relativePath() . ' vs ' . $codebaseResource->relativePath() . ' - ' . $zipResource->modifiedDate()->toDateTimeString() .
                                ' vs ' .
                                $codebaseResource->modifiedDate()->toDateTimeString());



                        if ($zipResource->modifiedDate()->greaterThanOrEqualTo($codebaseResource->modifiedDate()) &&
                           $codebaseResource->type() == 'file') {
                            $toRemove = true;
                        }

                        return false;
                    }
                });

                if ($toRemove) {
                    $this->info('--REMOVING ' . $codebaseResource->relativePath());
                }

                return $toRemove;
            });

            $this->info('');
            $this->info('Codebase resources count: ' . $codebase->values()->count());

            dd('---');

            $resource = $zipResources->first();

            dd($cleanCodebase->first(), $resource->getName(), $resource->isFolder(), Carbon::createFromTimestamp($resource->getMtime()));
        }
    }

    public function getModifiedDateFromZipInnerResource($path)
    {
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

class ZipResource
{
    protected $modifiedDate = null;
    protected $relativePath = null;

    public function __construct(ZipInfo $zipInfo)
    {
        $this->relativePath = $zipInfo->getName();
        $this->modifiedDate = timestamp_to_carbon($zipInfo->getMtime());
    }

    public function realPath()
    {
        return Str::endsWith($this->realPath, '/') ? substr($this->realPath, 0, -1) : $this->realPath;
    }

    public function relativePath()
    {
        return Str::endsWith($this->relativePath, '/') ? substr($this->relativePath, 0, -1) : $this->relativePath;
    }

    public function modifiedDate()
    {
        return $this->modifiedDate;
    }
}

class FileResource
{
    protected $modifiedDate = null;
    protected $createdDate = null;
    protected $realPath = null;
    protected $relativePath = null;
    protected $type = null;

    public function __construct(string $realPath)
    {
        if (is_file($realPath) || is_dir($realPath)) {
            // Apply transformations.
            $this->realPath = unix_separator_path($realPath);
            $this->relativePath = unix_separator_path(substr($realPath, strlen(base_path()) + 1));
            $this->modifiedDate = timestamp_to_carbon(filemtime($this->realPath));
            $this->createdDate = timestamp_to_carbon(filectime($this->realPath));

            $this->type = is_file($realPath) ? 'file' : 'folder';

            return $this;
        }

        return null;
    }

    public function type()
    {
        return $this->type;
    }
    public function realPath()
    {
        return Str::endsWith($this->realPath, '/') ? substr($this->realPath, 0, -1) : $this->realPath;
    }

    public function relativePath()
    {
        return Str::endsWith($this->relativePath, '/') ? substr($this->relativePath, 0, -1) : $this->relativePath;
    }

    public function modifiedDate()
    {
        return $this->modifiedDate;
    }

    public function createdDate()
    {
        return $this->createdDate;
    }
}
