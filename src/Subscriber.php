<?php

namespace WebHooker;

class Subscriber
{
    /**
     * @var ApiClient
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

    public function __construct(ApiClient $client, $id, $name)
    {
        $this->client = $client;
        $this->id = $id;
        $this->name = $name;
    }

    public function jsonSubscription($tenant, $url, $secret)
    {
        return $this->subscription('application/json', $tenant, $url, $secret);
    }

    public function xmlSubscription($tenant, $url, $secret)
    {
        return $this->subscription('application/xml', $tenant, $url, $secret);
    }

    public function subscription($format, $tenant, $url, $secret)
    {
        return new SubscriptionBuilder($this->client, $this->id, $format, $tenant, $url, $secret);
    }

    public function unsubscribe($tenant, $events = null)
    {
        return new UnsubscribeBuilder($this->client, $this->id, $tenant, $events);
    }
}
