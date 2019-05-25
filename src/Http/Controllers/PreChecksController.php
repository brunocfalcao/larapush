<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Brunocfalcao\Larapush\Services\Remote;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

/**
 * Controller that executes the necessary pre-checks before starting to
 * receive your codebase.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class PreChecksController extends RemoteBaseController
{
    public function __invoke()
    {
        Remote::preChecks();

        return response_payload();
    }
}
