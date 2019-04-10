<?php

namespace Brunocfalcao\Larapush\Concerns;

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
