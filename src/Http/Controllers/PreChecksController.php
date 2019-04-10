<?php

namespace Brunocfalcao\Larapush\Http\Controllers;

use Brunocfalcao\Larapush\Support\Remote;
use Brunocfalcao\Larapush\Abstracts\RemoteBaseController;

final class PreChecksController extends RemoteBaseController
{
    public function __invoke()
    {
        Remote::preChecks();

        return response_payload(true);
    }
}
