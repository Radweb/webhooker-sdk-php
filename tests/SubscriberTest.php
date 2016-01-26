<?php

namespace WebHooker\Test;

use Mockery as m;
use WebHooker\ApiClient;
use WebHooker\Subscriber;
use WebHooker\SubscriptionBuilder;

class SubscriberTest extends TestCase
{
    /** @test */
    public function it_returns_a_SubscriptionBuilder_configured_for_json()
    {
        $api = m::mock(ApiClient::class);

        $builder = (new Subscriber($api, 'foo', 'X Name'))->jsonSubscription('a', 'b', 'c');

        $this->assertEquals(new SubscriptionBuilder($api, 'foo', 'application/json', 'a', 'b', 'c'), $builder);
    }

    /** @test */
    public function it_returns_a_SubscriptionBuilder_configured_for_xml()
    {
        $api = m::mock(ApiClient::class);

        $builder = (new Subscriber($api, 'foo', 'X Name'))->xmlSubscription('a', 'b', 'c');

        $this->assertEquals(new SubscriptionBuilder($api, 'foo', 'application/xml', 'a', 'b', 'c'), $builder);
    }
}