<?php

namespace WebHooker;

class MessageSender
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var string
     */
    private $tenant;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $payloads = [];

    public function __construct(HttpClient $client, $tenant, $type)
    {
        $this->client = $client;
        $this->tenant = $tenant;
        $this->type = $type;
    }

    /**
     * @param mixed $body
     *
     * @return $this
     */
    public function json($body)
    {
        $this->payloads['application/json'] = $this->toJson($body);

        return $this;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function xml($body)
    {
        $this->payloads['application/xml'] = $body;

        return $this;
    }

    /**
     * @param mixed $body
     *
     * @return Message
     */
    public function send($body = null)
    {
        if (!is_null($body)) {
            $this->json($body);
        }

        $response = $this->client->send('POST', '/messages', [
          'tenant' => $this->tenant,
          'type' => $this->type,
          'payload' => $this->payloads,
        ]);

        $json = json_decode($response->getBody());

        return new Message($json->id, $json->tenant, $json->type, $json->formats, $json->recipients);
    }

    private function toJson($body)
    {
        return is_string($body) ? $body : json_encode($body);
    }
}
