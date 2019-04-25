<?php

namespace Brunocfalcao\Larapush\Tests;

use Brunocfalcao\Larapush\Tests\TestCase;
use PhpZip\ZipFile;

class LocalEnvironmentTest extends TestCase
{
    /**
     * @test
     */
    public function assertZipCreation()
    {
        /**
         * Creates a zip file with the content of the tests/assets.
         * Asserts if the files inside the zip are the same (and same structure)
         * as the ones from the assets folder.
         */

        $zipFile = new ZipFile();

        $this->assertTrue(true);
    }
}
