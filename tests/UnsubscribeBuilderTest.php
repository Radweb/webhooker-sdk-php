<?php

namespace WebHooker\Test;

use Mockery as m;
use WebHooker\ApiClient;
use WebHooker\Subscription;
use WebHooker\UnsubscribeBuilder;
use WebHooker\UnsubscriptionBuilder;

class UnsubscribeBuilderTest extends TestCase
{
	/** @test */
	public function it_can_be_configured_with_tenant_and_events()
	{
		$subscriberId = 'foo';
		$tenantKey = 'account-1';
		$events = ['inspection.completed'];

		$api = m::mock(ApiClient::class)
			->shouldReceive('send')
			->with('POST', '/subscribers/'.$subscriberId.'/subscriptions/unsubscribe', [
				'tenant' => $tenantKey,
				'events' => $events,
			])
			->once()
			->getMock();

		$response = (new UnsubscribeBuilder($api, $subscriberId, $tenantKey, $events))->save();

		$this->assertThat($response, $this->isNull());
	}
}
