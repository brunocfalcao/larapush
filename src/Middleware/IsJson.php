<?php

namespace Brunocfalcao\Larapush\Middleware;

use Closure;

/**
 * Middleware that verifies if the request mime data is json.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
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
