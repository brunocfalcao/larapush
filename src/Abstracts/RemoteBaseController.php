<?php

namespace Brunocfalcao\Larapush\Abstracts;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * Parent class used for all Remove Controller classes.
 * Allows to destroy any client access token that is still active on the
 * system for the respective configured Larapush client id.
 *
 * @category   Larapush
 * @package    brunocfalcao/larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
abstract class RemoteBaseController extends Controller
{
    public function __destruct()
    {
        /*
         * Any Larapush controller transaction will disable its own (and any other)
         * client access grant token that is still active for the client id defined
         * for Larapush. This is done to avoid client access token grants utilization
         * redundancy. Meaning a HTTP transaction will have a single unique client
         * access grant token usage.
         */
        DB::table('oauth_access_tokens')
          ->where('client_id', app('config')->get('larapush.oauth.client'))
          ->update(['revoked' => true]);
    }
}
