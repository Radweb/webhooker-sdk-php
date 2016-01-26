<?php

namespace WebHooker;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var Config
     */
    private $config;

    public function __construct(HttpClient $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string      $method
     * @param string      $path
     * @param string|null $body
     *
     * @return ResponseInterface
     */
    public function send($method, $path, $body = null)
    {
        $headers = [
            'x-api-key' => $this->config->getApiKey(),
            'content-type' => 'application/json',
        ];

        $url = $this->config->getDomain().$path;

        $body = $body ? json_encode($body) : null;

        return $this->client->send(new Request($method, $url, $headers, $body));
    }
}
