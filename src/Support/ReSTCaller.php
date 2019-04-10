<?php

namespace Brunocfalcao\Larapush\Support;

use Zttp\Zttp;
use Zttp\ConnectionException;
use GuzzleHttp\Exception\RequestException;

final class ReSTCaller
{
    public static function __callStatic($method, $args)
    {
        return RequestPayload::new()->{$method}(...$args);
    }
}

final class RequestPayload
{
    private const HTTP_VERB_GET = 'get';
    private const HTTP_VERB_POST = 'post';

    private $payload = [];
    private $headers = [];
    private $verb = self::HTTP_VERB_GET;
    private $accessToken = null;

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
        } catch (ConnectionException | RequestException $e) {
            $exception = $e;
        }

        return new ResponsePayload($response ?? null, $exception ?? null);
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }
}
