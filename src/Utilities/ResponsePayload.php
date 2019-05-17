<?php

namespace Brunocfalcao\Larapush\Utilities;

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
    public $isOk = false;
    public $exception = null;
    public $response = null;
    public $payload = null;

    public function __construct(ZttpResponse $response = null, ?\Exception $exception)
    {
        // The native exception data in case a connection exception was raised.
        if (isset($exception)) {
            $this->exception = new \StdClass;
            $this->exception->message = @$exception->getMessage();
            $this->exception->file = @$exception->getFile();
            $this->exception->line = @$exception->getLine();
        }

        // The native ZttpResponse object (with or without data, doesn't matter).
        if (isset($response)) {
            $this->response = $response;
        }

        if ($response !== null) {
            // In case json data was returned, let's add to our payload attribute.
            if ($response->json() !== null) {
                $this->payload = new \StdClass;
                $this->payload = $response->json();
            }
            // Computation of the general result based on the ZttpResponse status.
            $this->isOk = $response->isOk() && $response->status() == 200;
        }
    }
}
