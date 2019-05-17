<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

/**
 * Controller that responds to a "are you there?" contact from your
 * local development computer.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class PingController extends RemoteBaseController
{
    public function __invoke()
    {
        return response_payload();
    }
}
