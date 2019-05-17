<?php

namespace Brunocfalcao\Larapush\Exceptions;

use Exception as ParentException;
use Illuminate\Support\Facades\Log;

final class LocalException extends ParentException
{
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
        parent::__construct($this->message);
    }

    public function report()
    {
        Log::error($this->message);
    }
}
