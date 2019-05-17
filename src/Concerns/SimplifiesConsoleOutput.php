<?php

namespace Brunocfalcao\Larapush\Concerns;

/**
 * Trait used to shortcut UI interactions in the commands.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
trait SimplifiesConsoleOutput
{
    protected function showHero()
    {
        $this->bulkInfo(1, ascii_title(), 1);
    }

    protected function bulkInfo(int $crBefore, string $message = null, int $crAfter = 0)
    {
        while ($crBefore > 0) {
            $this->info('');
            $crBefore--;
        }

        if ($message) {
            $this->info($message);
        }

        while ($crAfter > 0) {
            $this->info('');
            $crAfter--;
        }
    }
}
