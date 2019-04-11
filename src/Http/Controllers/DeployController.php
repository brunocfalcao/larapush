<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Brunocfalcao\Larapush\Utilities\Remote;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

final class DeployController extends RemoteBaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);

        if ($validator->fails()) {
            return response_payload(false, ['message'=> $validator->errors()->first()], 201);
        }

        Remote::unzipCodebase($request->input('transaction'));

        return response_payload(true);
    }
}
