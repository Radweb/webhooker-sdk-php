<?php

namespace WebHooker\Test;

use Mockery as m;
use WebHooker\ApiClient;
use WebHooker\MessageSender;
use WebHooker\Subscriber;
use WebHooker\WebHooker;

class WebHookerTest extends TestCase
{
    /** @test */
    public function it_can_addSubscriber()
    {
        $api = m::mock(ApiClient::class)
          ->shouldReceive('send')
          ->with('POST', '/subscribers', ['name' => 'Sample Subscriber'])
          ->andReturn(['id' => 'iU4s', 'name' => 'Sample Subscriber'])
          ->once()
          ->getMock();

        $subscriber = (new WebHooker($api))->addSubscriber('Sample Subscriber');

        $this->assertInstanceOf(Subscriber::class, $subscriber);
        $this->assertEquals('Sample Subscriber', $subscriber->name);
        $this->assertEquals('iU4s', $subscriber->id);
    }

    /** @test */
    public function it_returns_a_subscriber_by_id()
    {
        $api = m::mock(ApiClient::class);

        $subscriber = (new WebHooker($api))->subscriber('Duh2');

        $this->assertEquals(new Subscriber($api, 'Duh2', null), $subscriber);
    }

    /** @test */
    public function it_creates_a_MessageSender_for_notify()
    {
        $api = m::mock(ApiClient::class);

        $out = (new WebHooker($api))->notify('tenant-key-here', 'the-event-name-here');

        $expected = new MessageSender($api, 'tenant-key-here', 'the-event-name-here');

        $this->assertEquals($expected, $out);
    }
}
