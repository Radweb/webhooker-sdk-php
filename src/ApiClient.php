<?php

namespace WebHooker;

use GuzzleHttp\Psr7\Request;
use WebHooker\Exceptions\Exception;
use WebHooker\Exceptions\ExpiredException;
use WebHooker\Exceptions\InvalidRequestException;
use WebHooker\Exceptions\NotFoundException;
use WebHooker\Exceptions\UnauthorisedRequestException;
use WebHooker\Exceptions\UnknownClientException;
use WebHooker\Exceptions\UnknownServerException;

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
     * @throws UnauthorisedRequestException
     *
     * @return array
     */
    public function send($method, $path, $body = null)
    {
        $headers = [
            'x-api-key' => $this->config->getApiKey(),
            'content-type' => 'application/json',
        ];

        $url = $this->config->getDomain().$path;

        $body = $body ? json_encode($body) : null;

        $response = $this->client->send(new Request($method, $url, $headers, $body));

        $body = json_decode($response->getBody(), true);

        $status = $response->getStatusCode();

        if ($status < 400) {
            return $body;
        } else {
            throw $this->makeException($this->getExceptionName($status), $body);
        }
    }

    private function getExceptionName($status)
    {
        if ($status == 400) {
            return InvalidRequestException::class;
        }
        if ($status == 401) {
            return UnauthorisedRequestException::class;
        }
        if ($status == 402) {
            return ExpiredException::class;
        }
        if ($status == 404) {
            return NotFoundException::class;
        }
        if ($status <= 499) {
            return UnknownClientException::class;
        }
        if ($status >= 400) {
            return UnknownServerException::class;
        }

        return Exception::class;
    }

    private function makeException($exceptionName, $body)
    {
        if (is_array($body) && array_key_exists('message', $body) && array_key_exists('details', $body)) {
            return new $exceptionName($body['message'], $body['details']);
        } else {
            return new $exceptionName();
        }
    }
}
