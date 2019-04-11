<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

final class PingController extends RemoteBaseController
{
    public function __invoke()
    {
        return response_payload();
    }
}
