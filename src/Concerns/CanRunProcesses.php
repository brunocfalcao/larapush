<?php

namespace Brunocfalcao\Larapush\Concerns;

use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Trait used to allow process to be executed.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
trait CanRunProcesses
{
    protected function runProcess($command, $path = null)
    {
        $path = $path ?? getcwd();

        $process = (Process::fromShellCommandline($command, $path))->setTimeout(null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run();
        /*
        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
        */

        if (! $process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
