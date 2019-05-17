<?php

namespace Brunocfalcao\Larapush\Utilities;

/**
 * Class that stores a codebase repository structure.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class CodebaseRepository
{
    private $runbook;
    private $codebaseStream;
    private $transaction;

    public function withCodebaseStream(string $codebaseStream)
    {
        $this->codebaseStream = $codebaseStream;

        return $this;
    }

    public function withTransaction(string $transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function withRunbook(string $runbook)
    {
        $this->runbook = $runbook;

        return $this;
    }

    public function codebaseStream()
    {
        return $this->codebaseStream;
    }

    public function runbook()
    {
        return $this->runbook;
    }

    public function transaction()
    {
        return $this->transaction;
    }
}
