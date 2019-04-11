<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Brunocfalcao\Larapush\Utilities\Remote;
use Illuminate\Support\Facades\Validator;
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

        /*
        larapush_rescue(function () use ($request) {
            Remote::runPreScripts($request->input('transaction'));
        }, function ($exception) use (&$responsePayload) {
            $responsePayload = response_payload(['message' => $exception->getMessage()], 403);
        });
        */

        return response_payload();
    }
}
