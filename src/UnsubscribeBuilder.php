<?php

namespace WebHooker;

class UnsubscribeBuilder
{
    /**
     * @var ApiClient
     */
    private $client;

    private $subscriberId;

    private $tenant;

    private $events = [];

    public function __construct(ApiClient $client, $subscriberId, $tenant, $events = [])
    {
        $this->client = $client;
        $this->subscriberId = $subscriberId;
        $this->tenant = $tenant;
        $this->events = $events;
    }

    public function delete($subscriptionId)
    {
        return $this->client->send('DELETE', '/subscribers/'.$this->subscriberId.'/subscriptions/'.$subscriptionId);
    }

    public function save()
    {
        $body = [
            'tenant' => $this->tenant,
            'events' => $this->events,
        ];

        return $this->client->send('POST', '/subscribers/'.$this->subscriberId.'/subscriptions/unsubscribe', $body);
    }
}
