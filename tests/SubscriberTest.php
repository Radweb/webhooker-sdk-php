<?php

namespace WebHooker\Test;

use Mockery as m;
use WebHooker\HttpClient;
use WebHooker\Subscriber;
use WebHooker\SubscriptionBuilder;

class SubscriberTest extends TestCase
{
    /** @test */
    public function it_returns_a_SubscriptionBuilder_configured_for_json()
    {
        $http = m::mock(HttpClient::class);

        $builder = (new Subscriber($http, 'foo', 'X Name'))->jsonSubscription('a', 'b', 'c');

        $this->assertEquals(new SubscriptionBuilder($http, 'foo', 'application/json', 'a', 'b', 'c'), $builder);
    }

    /** @test */
    public function it_returns_a_SubscriptionBuilder_configured_for_xml()
    {
        $http = m::mock(HttpClient::class);

        $builder = (new Subscriber($http, 'foo', 'X Name'))->xmlSubscription('a', 'b', 'c');

        $this->assertEquals(new SubscriptionBuilder($http, 'foo', 'application/xml', 'a', 'b', 'c'), $builder);
    }
}