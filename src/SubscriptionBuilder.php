<?php

namespace WebHooker;

class SubscriptionBuilder
{
    /**
     * @var ApiClient
     */
    private $client;

    private $subscriberId;

    private $format;

    private $tenant;

    private $url;

    private $secret;

    private $basicAuth;

    private $legacy = [];

    private $events = [];

    public function __construct(ApiClient $client, $subscriberId, $format, $tenant, $url, $secret, $events = [])
    {
        $this->client = $client;
        $this->subscriberId = $subscriberId;
        $this->format = $format;
        $this->tenant = $tenant;
        $this->url = $url;
        $this->secret = $secret;
        $this->events = $events;
    }

    public function basicAuth($username, $password)
    {
        $this->basicAuth = compact('username', 'password');

        return $this;
    }

    public function legacyPayload($payloadField)
    {
        $this->legacy['payload'] = $payloadField;

        return $this;
    }

    public function save()
    {
        $body = [
            'format' => $this->format,
            'tenant' => $this->tenant,
            'url' => $this->url,
            'secret' => $this->secret,
            'events' => $this->events,
        ];

        if ($this->basicAuth) {
            $body['auth'] = $this->basicAuth;
        }

        if ($this->legacy) {
            $body['legacy'] = $this->legacy;
        }

        $response = $this->client->send('POST', '/subscribers/'.$this->subscriberId.'/subscriptions', $body);

        $subscription = new Subscription(
            $response['id'],
            $response['subscriber_id'],
            $response['tenant'],
            $response['format'],
            $response['url']
        );

        $subscription->setUsesBasicAuth($response['uses_basic_auth']);

        if ($response['legacy']) {
            $subscription->setLegacyPayload($response['legacy']['payload']);
        }

        return $subscription;
    }
}
