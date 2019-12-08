<?php

use Brunocfalcao\Larapush\Structures\ResponsePayload;
use Illuminate\Support\Carbon;

if (! function_exists('ascii_title')) {
    function ascii_title()
    {
        /*
         * Credits to patorjk.com/software/taag/
         */
        return "
  _                               _
 | |   __ _ _ _ __ _ _ __ _  _ __| |_
 | |__/ _` | '_/ _` | '_ \ || (_-< ' \
 |____\__,_|_| \__,_| .__/\_,_/__/_||_|
                    |_|
        ";
    }
}

if (! function_exists('capsule')) {
    function capsule(bool $result, $message = null, $payload = null)
    {
        $capsule = new stdClass();
        $capsule->ok = $result;
        $capsule->payload = $payload;
        $capsule->message = $message;

        return $capsule;
    }
}

if (! function_exists('larapush_remote_url')) {
    function larapush_remote_url($path)
    {
        return app('config')->get('larapush.remote.url').
           larapush_url($path);
    }
}

if (! function_exists('larapush_url')) {
    function larapush_url($url)
    {
        return config('larapush.remote.suffix')."/{$url}";
    }
}

if (! function_exists('append_line_to_env')) {
    function append_line_to_env(string $key, $value)
    {
        return file_put_contents(base_path('.env'), PHP_EOL."{$key}={$value}", FILE_APPEND);
    }
}

if (! function_exists('response_payload')) {
    function response_payload($payload = [], $statusCode = 200)
    {
        return response(json_encode($payload), $statusCode);
    }
}

if (! function_exists('larapush_storage_path')) {
    function larapush_storage_path($path = null)
    {
        return app('config')->get('larapush.storage.path')."/{$path}";
    }
}

if (! function_exists('generate_transaction_code')) {
    function generate_transaction_code()
    {
        return date('Ymd-His').'-'.strtoupper(str_random(5));
    }
}

if (! function_exists('larapush_rescue')) {
    function larapush_rescue(callable $callback, $rescue = null)
    {
        try {
            return $callback();
        } catch (Throwable $exception) {
            report($exception);

            return $rescue($exception);
        }
    }
}

if (! function_exists('get_response_payload_friendly_message')) {
    function get_response_payload_friendly_message(ResponsePayload $response)
    {
        // In case a connection/request exception is active.
        if ($response->exception() !== null) {
            return $response->exception()->message.
               ' (line '.
               $response->exception()->line.
               ') in '.
               $response->exception()->file;
        }

        // In case a response payload exists.
        if ($response->payload() !== null) {
            $payload = (object) $response->payload();

            $message = 'Undefined response message. Please check the Laravel logs.';

            if (isset($payload->message)) {
                $message = $payload->message;
            }

            if (isset($payload->error)) {
                $message = $payload->error;
            }

            if (isset($payload->line)) {
                $message .= " (line $payload->line)";
            }

            if (isset($payload->file)) {
                $message .= " (file $payload->file)";
            }

            return $message;
        }
    }
}

if (! function_exists('glob_recursive')) {
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }
}

if (! function_exists('unix_separator_path')) {
    function unix_separator_path($path)
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }
}

if (! function_exists('timestamp_to_carbon')) {
    function timestamp_to_carbon($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp);
    }
}
