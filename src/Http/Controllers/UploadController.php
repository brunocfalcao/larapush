<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Brunocfalcao\Larapush\Services\Remote;
use Brunocfalcao\Larapush\Utilities\CodebaseRepository;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

/**
 * Controller that receives your codebase and scripts to run.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class UploadController extends RemoteBaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codebase'    => 'required',
            'transaction' => 'required',
            'runbook'     => 'required',
        ]);

        if ($validator->fails()) {
            return response_payload(['message'=> $validator->errors()->first()], 201);
        }

        $repository = (new CodebaseRepository())
                          ->withCodebaseStream(base64_decode($request->input('codebase')))
                          ->withRunbook($request->input('runbook'))
                          ->withTransaction($request->input('transaction'));

        Remote::storeRepository($repository);

        return response_payload();
    }
}
