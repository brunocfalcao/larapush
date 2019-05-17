<?php

namespace Brunocfalcao\Larapush\Abstracts;

use Illuminate\Console\Command;
use Brunocfalcao\Larapush\Concerns\CanRunProcesses;
use Brunocfalcao\Larapush\Concerns\SharedInstallerActions;
use Brunocfalcao\Larapush\Concerns\SimplifiesConsoleOutput;
use Brunocfalcao\Larapush\Concerns\ValidatesConsoleArguments;

/**
 * Parent class used for the installer commands.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
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
        // Clear output.
        echo "\033[2J\033[;H";

        $this->info(ascii_title());
    }
}
