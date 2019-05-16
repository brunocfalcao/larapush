<?php

namespace Brunocfalcao\Larapush\Utilities;

final class Local
{
    public static function __callStatic($method, $args)
    {
        return LocalOperation::new()->{$method}(...$args);
    }
}
