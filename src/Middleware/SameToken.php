<?php

namespace Brunocfalcao\Larapush\Middleware;

use Closure;

/**
 * Middleware that checks if your local development computer token is
 * equal to the one on your web server.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class SameToken
{
    public function handle($request, Closure $next)
    {
        if ($request->input('larapush-token') !== app('config')->get('larapush.token')) {
            return response()->json(['error' => 'Local and remote tokens are different. Please check both local and remote configuration tokens']);
        }

        return $next($request);
    }
}
