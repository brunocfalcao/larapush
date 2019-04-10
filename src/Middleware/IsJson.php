<?php

namespace Brunocfalcao\Larapush\Middleware;

use Closure;

final class IsJson
{
    public function handle($request, Closure $next)
    {
        if (! $request->isJson()) {
            return response()->json(
                ['error' => 'Request Header Type not accepted. Expecting JSON.'],
                406
            );
        }

        return $next($request);
    }
}
