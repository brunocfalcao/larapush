<?php

namespace Brunocfalcao\Larapush\Tests;

use Brunocfalcao\Larapush\Utilities\Local;
use Brunocfalcao\Larapush\Utilities\CodebaseRepository;

class LocalEnvironmentTest extends TestCase
{
    /**
     * @test
     */
    public function assertLocalCreateTransaction()
    {
        $this->artisan('larapush:install-remote');

        $transaction = generate_transaction_code();
        (new CodebaseRepository)->createRepository($transaction);

        // Verify if local repository was created, along with the runbook.json and the codebase.zip file.
        $transactionRepository = app('config')->get('larapush.storage.path').'/'.$transaction;

        // Directory exists?
        if (! is_dir($transactionRepository)) {
            return $this->fail();
        }

        // codebase.zip and runbook.json exists?
        if (! is_file("$transactionRepository/codebase.zip") || ! is_file("$transactionRepository/runbook.json")) {
            return $this->fail();
        }

        $this->assertTrue(true);
    }
}
