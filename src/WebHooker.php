<?php

namespace WebHooker;

use GuzzleHttp\Client;

class WebHooker
{
    /**
     * @var ApiClient
     */
    private $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param $config
     *
     * @return WebHooker
     */
    public static function usingGuzzle($config)
    {
        if (!$config instanceof Config) {
            $config = Config::make($config); // passing in API Key
        }

        return new self(new ApiClient(new GuzzleHttpClient(new Client()), $config));
    }

    public function addSubscriber($name)
    {
        $response = $this->client->send('POST', '/subscribers', compact('name'));

        $json = json_decode($response->getBody());

        return new Subscriber($this->client, $json->id, $json->name);
    }

    public function subscriber($id)
    {
        return new Subscriber($this->client, $id, null);
    }

    public function notify($tenant, $eventName)
    {
        return new MessageSender($this->client, $tenant, $eventName);
    }
}
