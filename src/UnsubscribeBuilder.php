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

	private $events;

	private $basicAuth;

	private $legacy = [];

	public function __construct(ApiClient $client, $subscriberId, $tenant, $events = null)
	{
		$this->client = $client;
		$this->subscriberId = $subscriberId;
		$this->tenant = $tenant;
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
			'tenant' => $this->tenant,
			'events' => $this->events,
		];

		if ($this->basicAuth) {
			$body['auth'] = $this->basicAuth;
		}

		if ($this->legacy) {
			$body['legacy'] = $this->legacy;
		}

		return $this->client->send('POST', '/subscribers/'.$this->subscriberId.'/subscriptions/unsubscribe', $body);
	}
}