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

    public function __construct(ApiClient $client, $subscriberId, $format, $tenant, $url, $secret)
    {
        $this->client = $client;
        $this->subscriberId = $subscriberId;
        $this->format = $format;
        $this->tenant = $tenant;
        $this->url = $url;
        $this->secret = $secret;
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
        ];

        if ($this->basicAuth)
        {
            $body['auth'] = $this->basicAuth;
        }

        if ($this->legacy)
        {
            $body['legacy'] = $this->legacy;
        }

        $response = $this->client->send('POST', '/subscribers/'.$this->subscriberId.'/subscriptions', $body);

        $json = json_decode($response->getBody());

        $subscription = new Subscription($json->id, $json->subscriber_id, $json->tenant, $json->format, $json->url);
        $subscription->setUsesBasicAuth($json->uses_basic_auth);
        if ($json->legacy) {
            $subscription->setLegacyPayload($json->legacy->payload);
        }

        return $subscription;
    }
}