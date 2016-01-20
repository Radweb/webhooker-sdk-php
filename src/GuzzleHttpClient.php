<?php

namespace WebHooker;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleHttpClient implements HttpClient
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Config
     */
    private $config;

    public function __construct(ClientInterface $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string $method
     * @param string $path
     * @param string|null $body
     * @return ResponseInterface
     */
    public function send($method, $path, $body = null)
    {
        return $this->client->request($method, $this->config->getDomain() . $path, [
            'http_errors' => true,
            'body' => json_encode($body),
            'headers' => [
                'X-API-Key' => $this->config->getApiKey(),
                'Content-Type' => 'application/json',
            ],
        ]);
    }
}