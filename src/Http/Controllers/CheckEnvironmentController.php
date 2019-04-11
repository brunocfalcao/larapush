<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

final class CheckEnvironmentController extends RemoteBaseController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'environments' => 'required',
        ]);

        if ($validator->fails()) {
            return response_payload(['message'=> $validator->errors()->first()], 201);
        }

        $envs = explode(',', str_replace(' ', '', $request->input('environments')));

        if (in_array(app()->environment(), $envs)) {
            return response_payload(['prompt' => true]);
        }

        return response_payload(['prompt' => false]);
    }
}
