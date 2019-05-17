<?php

namespace Brunocfalcao\Larapush\Utilities;

/**
 * Class that stores the script type constants.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class ScriptType
{
    public const ARTISAN = 'artisan';
    public const CLASSMETHOD = 'class_method';
    public const SHELLCMD = 'shell_cmd';
}
