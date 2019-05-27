<?php

namespace Brunocfalcao\Larapush\Utilities;

use Brunocfalcao\Larapush\Utilities\RequestPayload;

/**
 * Main Class that is used for local REsT calls.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class ReSTCaller
{
    public static function __callStatic($method, $args)
    {
        return RequestPayload::new()->{$method}(...$args);
    }
}
