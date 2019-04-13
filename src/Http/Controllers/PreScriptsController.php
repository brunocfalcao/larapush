<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Brunocfalcao\Larapush\Utilities\Remote;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

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

        $responsePayload = response_payload();

        Remote::runPreScripts($request->input('transaction'));

        return response_payload();
    }
}
