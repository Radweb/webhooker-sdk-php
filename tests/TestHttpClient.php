<?php

namespace WebHooker\Test;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WebHooker\HttpClient;

class TestHttpClient implements HttpClient
{
    public $requests = [];

    public $responses = [];

    public function __construct($responseQueue = [])
    {
        $this->responses = $responseQueue;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function send(RequestInterface $request)
    {
        $this->requests[] = $request;

        return array_shift($this->responses);
    }
}
