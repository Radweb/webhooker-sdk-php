<?php

namespace WebHooker;

class Subscriber
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    public function __construct(HttpClient $client, $id, $name)
    {
        $this->client = $client;
        $this->id = $id;
        $this->name = $name;
    }

    public function receive($tenant, $format, $url, $secret)
    {
        $response = $this->client->send('POST', '/subscribers/' . $this->id . '/subscriptions',
          compact('tenant', 'format', 'url', 'secret'));

        $json = json_decode($response->getBody());

        return new Subscription($json->id, $json->subscriber_id, $json->tenant, $json->format, $json->url);
    }

    public function receiveJson($tenant, $url, $secret)
    {
        return $this->receive($tenant, 'application/json', $url, $secret);
    }

    public function receiveXml($tenant, $url, $secret)
    {
        return $this->receive($tenant, 'application/xml', $url, $secret);
    }
}