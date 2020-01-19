<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;
use Brunocfalcao\Larapush\Services\Remote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controller that executes your scripts before a successful deployment.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class PreScriptsController extends RemoteBaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return response_payload(['message'=> $validator->errors()->first()], 403);
        }

        Remote::runPreScripts($request->input('transaction'));

        return response_payload();
    }
}
