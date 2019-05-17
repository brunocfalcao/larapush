<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Brunocfalcao\Larapush\Utilities\Remote;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

/**
 * Controller that executes your scripts after a successful deployment.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class PostScriptsController extends RemoteBaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return response_payload(['message'=> $validator->errors()->first()], 201);
        }

        Remote::runPostScripts($request->input('transaction'));

        return response_payload();
    }
}
