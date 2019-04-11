<?php

namespace Brunocfalcao\Larapush\Utilities;

use Chumper\Zipper\Facades\Zipper;
use Illuminate\Support\Facades\Storage;
use Brunocfalcao\Larapush\Concerns\CanRunProcesses;
use Brunocfalcao\Larapush\Exceptions\RemoteException;

final class Remote
{
    public static function __callStatic($method, $args)
    {
        return RemoteOperation::new()->{$method}(...$args);
    }
}

final class RemoteOperation
{
    use CanRunProcesses;

    const PREPUSH = 'pre_scripts';
    const POSTPUSH = 'post_scripts';

    public static function new(...$args)
    {
        return new self(...$args);
    }

    public function unzipCodebase(string $transaction) : void
    {
        if (Storage::disk('larapush')->exists("{$transaction}/codebase.zip")) {
            Zipper::make(larapush_storage_path("{$transaction}/codebase.zip"))->extractTo(base_path(), app('config')->get('larapush.codebase.blacklist'), 2);
        }
    }

    private function runScripts(string $type, string $transaction) : void
    {
        if (Storage::disk('larapush')->exists("{$transaction}/runbook.json")) {
            $resource = json_decode(Storage::disk('larapush')->get("{$transaction}/runbook.json"));

            collect(data_get($resource, "{$type}"))->each(function ($item) use ($transaction, $type) {
                $output = $this->runScript($item);

                if ($output !== null) {
                    Storage::disk('larapush')->append("{$transaction}/output_{$type}.json", "Command: {$item[0]}");
                    Storage::disk('larapush')->append("{$transaction}/output_{$type}.json", 'Output:');
                    Storage::disk('larapush')->append("{$transaction}/output_{$type}.json", "{$output}");
                }
            });
        }
    }

    public function runPostScripts(string $transaction) : void
    {
        $this->runScripts(self::POSTPUSH, $transaction);
    }

    public function runPreScripts(string $transaction) : void
    {
        $this->runScripts(self::PREPUSH, $transaction);
    }

    public function preChecks() : void
    {
        $storagePath = app('config')->get('larapush.storage.path');
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        if (! is_writable($storagePath)) {
            throw new RemoteException('Local storage directory not writeable');
        }
    }

    public function storeRepository(CodebaseRepository $repository) : void
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
        $output = null;

        $script = new Script($command);
        $output = $script->execute();

        return $output;
    }
}
