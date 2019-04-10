<?php

namespace Brunocfalcao\Larapush\Support;

use Illuminate\Support\Facades\Artisan;
use Brunocfalcao\Larapush\Concerns\CanRunProcesses;

final class Script
{
    use CanRunProcesses;

    public function __construct(array $scriptPayload)
    {
        $this->type = $scriptPayload[1];
        $this->command = $scriptPayload[0];
    }

    public function execute()
    {
        switch ($this->type) {
            case 'artisan':
                $error = Artisan::call($this->command);
                if ($error != 0) {
                    throw \Exception('There was an error on your Artisan command - '.Artisan::output());
                }

                return Artisan::output();
                break;

            case 'class_method':
                if (class_exists($this->command)) {
                    return (new $this->command)();
                }

                if (strpos($this->command, '@')) {
                    return app()->call($this->command);
                }
                break;

            case 'shell_cmd':
                $this->runProcess($this->command);
                break;
        }
    }
}
