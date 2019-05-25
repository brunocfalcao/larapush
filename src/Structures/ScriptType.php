<?php

namespace Brunocfalcao\Larapush\Structures;

/**
 * Class that stores the script type constants defined in your larapush.php
 * configuration file.
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
    /**
     * Artisan command.
     * E.g.: 'optimize:clear'.
     */
    public const ARTISAN = 'artisan';

    /**
     * Class method.
     * E.g.: 'myclass@mymethod'.
     * E.g.: MyClass::class (will call the magic method __invoke).
     */
    public const CLASSMETHOD = 'class_method';

    /**
     * A shell command.
     * E.g.: 'composer update'.
     */
    public const SHELLCMD = 'shell_cmd';
}
