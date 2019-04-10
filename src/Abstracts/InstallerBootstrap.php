<?php

namespace Brunocfalcao\Larapush\Abstracts;

use Illuminate\Console\Command;
use Brunocfalcao\Larapush\Concerns\CanRunProcesses;
use Brunocfalcao\Larapush\Concerns\SharedInstallerActions;
use Brunocfalcao\Larapush\Concerns\SimplifiesConsoleOutput;
use Brunocfalcao\Larapush\Concerns\ValidatesConsoleArguments;

abstract class InstallerBootstrap extends Command
{
    use CanRunProcesses;
    use SharedInstallerActions;
    use SimplifiesConsoleOutput;
    use ValidatesConsoleArguments;

    protected $steps;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Quick way to clear the screen :)
        echo "\033[2J\033[;H";

        $this->info(ascii_title());
    }
}
