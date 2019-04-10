<?php

namespace Brunocfalcao\Larapush\Exceptions;

use Exception;

final class AccessTokenException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
