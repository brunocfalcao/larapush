<?php

namespace Brunocfalcao\Larapush\Abstracts;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

abstract class RemoteBaseController extends Controller
{
    public function __destruct()
    {
        // Disable any active token for the larapush client.
        DB::table('oauth_access_tokens')
          ->where('client_id', app('config')->get('larapush.oauth.client'))
          ->update(['revoked' => true]);
    }
}
