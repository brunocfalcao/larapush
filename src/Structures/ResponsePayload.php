<?php

namespace Brunocfalcao\Larapush\Structures;

use Zttp\ZttpResponse;

/**
 * Class that stores a server response in the local environment.
 * All responses from the server are in json format (except HTTP connection exceptions).
 * Each ResponsePayload object is composed by 3 scopes:.
 *
 * $exception - If there was a connection/request exception under the HTTP layer.
 * $payload   - The actual response data that is received, in json format.
 * $response  - The native ZttpResponse object.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class ResponsePayload
{
    private $isOk = false;
    private $exception = null;
    private $response = null;
    private $payload = null;

    public function __construct(?ZttpResponse $response, ?\Exception $exception)
    {
        // The native exception data in case a connection exception was raised.
        if (isset($exception)) {
            $this->exception = (new \stdClass());

            try {
                $this->exception->message = $exception->getMessage();
            } catch (\Exception $e) {
                $this->exception->message = null;
            }

            try {
                $this->exception->file = $exception->getFile();
            } catch (\Exception $e) {
                $this->exception->file = null;
            }

            try {
                $this->exception->line = $exception->getLine();
            } catch (\Exception $e) {
                $this->exception->line = null;
            }
        }

        // The native ZttpResponse object (with or without data, doesn't matter).
        if (isset($response)) {
            $this->response = $response;
        }

        if ($response !== null) {
            // In case json data was returned, let's add to our payload attribute.
            if ($response->json() !== null) {
                $this->payload = (new \stdClass());
                $this->payload = $response->json();
            }
            // Computation of the general result based on the ZttpResponse status.
            $this->isOk = $response->isOk() && $response->status() === 200;
        }
    }

    public function exception()
    {
        return $this->exception;
    }

    public function isOk()
    {
        return $this->isOk;
    }

    public function response()
    {
        return $this->response;
    }

    public function payload()
    {
        return $this->payload;
    }
}
