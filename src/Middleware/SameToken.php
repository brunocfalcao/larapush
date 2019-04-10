<?php

namespace Brunocfalcao\Larapush\Middleware;

use Closure;

final class SameToken
{
    public function handle($request, Closure $next)
    {
        if ($request->input('larapush-token') != app('config')->get('larapush.token')) {
            return response()->json(['error' => 'Local and remote tokens are different. Please check both local and remote configuration tokens']);
        }

        return $next($request);
    }
}
