<?php

namespace WebHooker;

use Psr\Http\Message\ResponseInterface;

interface HttpClient
{
    /**
     * @param string $method
     * @param string $path
     * @param string|null $body
     * @return ResponseInterface
     */
    public function send($method, $path, $body = null);
}