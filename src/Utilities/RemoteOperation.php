<?php

namespace Brunocfalcao\Larapush\Utilities;

use PhpZip\ZipFile;
use Illuminate\Support\Facades\Storage;
use Brunocfalcao\Larapush\Concerns\CanRunProcesses;
use Brunocfalcao\Larapush\Exceptions\RemoteException;

/**
 * Class that executes all operations contexted in your web server.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class RemoteOperation
{
    use CanRunProcesses;

    protected const PREPUSH = 'pre_scripts';
    protected const POSTPUSH = 'post_scripts';

    public static function new(...$args)
    {
        return new self(...$args);
    }

    public function unzipCodebase(string $transaction): void
    {
        if (Storage::disk('larapush')->exists("{$transaction}/codebase.zip")) {
            $zip = new ZipFile();
            $zip->openFile(larapush_storage_path("{$transaction}/codebase.zip"))
                ->extractTo(base_path());
        }
    }

    private function runScripts(string $type, string $transaction): void
    {
        if (Storage::disk('larapush')->exists("{$transaction}/runbook.json")) {
            $resource = json_decode(Storage::disk('larapush')->get("{$transaction}/runbook.json"));

            collect(data_get($resource, "{$type}"))->each(function ($item) use ($transaction, $type) {
                $output = $this->runScript($item);

                if ($output !== null) {
                    Storage::disk('larapush')->append("{$transaction}/output_{$type}.log", "Command: {$item[0]}");
                    Storage::disk('larapush')->append("{$transaction}/output_{$type}.log", 'Output:');
                    Storage::disk('larapush')->append("{$transaction}/output_{$type}.log", "{$output}");
                }
            });
        }
    }

    public function runPostScripts(string $transaction): void
    {
        $this->runScripts(self::POSTPUSH, $transaction);
    }

    public function runPreScripts(string $transaction): void
    {
        $this->runScripts(self::PREPUSH, $transaction);
    }

    public function preChecks(): void
    {
        $storagePath = app('config')->get('larapush.storage.path');
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        if (! is_writable($storagePath)) {
            throw new RemoteException('Local storage directory not writeable');
        }
    }

    public function storeRepository(CodebaseRepository $repository): void
    {
        larapush_rescue(function () use ($repository) {
            // Create a new transaction folder inside the larapush storage.
            Storage::disk('larapush')->makeDirectory($repository->transaction());

            // Store the runbook, and the zip codebase file.
            Storage::disk('larapush')->put("{$repository->transaction()}/codebase.zip", $repository->codebaseStream());
            Storage::disk('larapush')->put("{$repository->transaction()}/runbook.json", $repository->runbook());
        }, function ($exception) {
            throw new RemoteException($exception->getMessage());
        });
    }

    private function runScript(array $command)
    {
        $script = new Script($command);

        return $script->execute();
    }
}
