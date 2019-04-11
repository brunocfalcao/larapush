<?php

namespace Brunocfalcao\Larapush\Abstracts;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
