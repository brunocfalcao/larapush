<?php

namespace Brunocfalcao\Larapush\Utilities;

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
