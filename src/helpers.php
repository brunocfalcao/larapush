<?php

use Illuminate\Support\Carbon;
use Brunocfalcao\Larapush\Utilities\ResponsePayload;

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

function capsule(bool $result, $message = null, $payload = null)
{
    $capsule = new stdClass();
    $capsule->ok = $result;
    $capsule->payload = $payload;
    $capsule->message = $message;

    return $capsule;
}

function larapush_remote_url($path)
{
    return app('config')->get('larapush.remote.url').
           larapush_url($path);
}

function larapush_url($url)
{
    return config('larapush.remote.suffix')."/{$url}";
}

function append_line_to_env(string $key, $value)
{
    return file_put_contents(base_path('.env'), PHP_EOL."{$key}={$value}", FILE_APPEND);
}

function response_payload($payload = [], $statusCode = 200)
{
    return response(json_encode($payload), $statusCode);
}

function larapush_storage_path($path = null)
{
    return app('config')->get('larapush.storage.path')."/{$path}";
}

function generate_transaction_code()
{
    return date('Ymd-His').'-'.strtoupper(str_random(5));
}

function larapush_rescue(callable $callback, $rescue = null)
{
    try {
        return $callback();
    } catch (Throwable $e) {
        report($e);

        return $rescue($e);
    }
}

function get_response_payload_friendly_message(ResponsePayload $response)
{
    // In case a connection/request exception is active.
    if ($response->exception !== null) {
        return $response->exception->message.
               ' (line '.
               $response->exception->line.
               ') in '.
               $response->exception->file;
    }

    // In case a response payload exists.
    if (isset($response->payload)) {
        $payload = (object) $response->payload;

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

function glob_recursive($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }

    return $files;
}

function unix_separator_path($path)
{
    return str_replace(DIRECTORY_SEPARATOR, '/', $path);
}

function timestamp_to_carbon($timestamp)
{
    return Carbon::createFromTimestamp($timestamp);
}
