<?php

namespace Brunocfalcao\Larapush\Utilities;

use Brunocfalcao\Larapush\Utilities\LocalOperation;

final class Local
{
    public static function __callStatic($method, $args)
    {
        return LocalOperation::new()->{$method}(...$args);
    }
}
