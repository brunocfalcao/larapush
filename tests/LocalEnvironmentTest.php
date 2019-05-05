<?php

namespace Brunocfalcao\Larapush\Tests;

use Brunocfalcao\Larapush\Utilities\Local;
use PhpZip\ZipFile;

class LocalEnvironmentTest extends TestCase
{
    /**
     * @test
     */
    public function assertLocalCreateTransaction()
    {
        Local::createRepository(generate_transaction_code());

        $this->assertTrue(true);
    }
}
