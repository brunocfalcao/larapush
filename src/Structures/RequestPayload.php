<?php

namespace Brunocfalcao\Larapush\Structures;

use Zttp\Zttp;
use Zttp\ConnectionException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class that will store a request payload.
 *
 * @category   Larapush
 * @author     Bruno Falcao <bruno.falcao@laraning.com>
 * @copyright  2019 Bruno Falcao
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html GPL v3
 * @version    Release: 1.0
 * @link       http://www.github.com/brunocfalcao/larapush
 */
final class RequestPayload
{
    private const HTTP_VERB_GET = 'get';
    private const HTTP_VERB_POST = 'post';

    private $payload = [];
    private $headers = [];
    private $verb = self::HTTP_VERB_GET;

    public function __construct()
    {
    }

    public function withHeader(string $key, string $value)
    {
        $this->headers = array_merge($this->headers, [$key => $value]);

        return $this;
    }

    public function withPayload(array $payload)
    {
        $this->payload = array_merge($this->payload, $payload);

        return $this;
    }

    public function asPost()
    {
        $this->verb = self::HTTP_VERB_POST;

        return $this;
    }

    public function asGet()
    {
        $this->verb = self::HTTP_VERB_GET;

        return $this;
    }

    public function call($url)
    {
        try {
            $response = Zttp::withHeaders($this->headers)
                            ->{$this->verb}($url, $this->payload);
        } catch (ConnectionException | RequestException $reqException) {
            $exception = $reqException;
        }

        return new ResponsePayload($response ?? null, $exception ?? null);
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }
}
