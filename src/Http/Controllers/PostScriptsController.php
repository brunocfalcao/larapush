<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Brunocfalcao\Larapush\Support\Remote;
use Illuminate\Support\Facades\Validator;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

final class PostScriptsController extends RemoteBaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return response_payload(false, ['message'=> $validator->errors()->first()], 201);
        }

        Remote::runPostScripts($request->input('transaction'));

        return response_payload(true);
    }
}
