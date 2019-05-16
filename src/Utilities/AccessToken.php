<?php

namespace Brunocfalcao\Larapush\Utilities;

class AccessToken
{
    public $expiresIn = null;
    public $token = null;

    public function __construct(int $expiresIn, string $token)
    {
        [$this->expiresIn, $this->token] = [$expiresIn, $token];
    }
}
