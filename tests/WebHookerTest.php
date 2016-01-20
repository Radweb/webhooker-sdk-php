<?php

namespace WebHooker\Test;

use GuzzleHttp\Psr7\Response;
use Mockery as m;
use WebHooker\HttpClient;
use WebHooker\Message;
use WebHooker\Subscriber;
use WebHooker\Subscription;
use WebHooker\WebHooker;

class WebHookerTest extends TestCase
{
    /** @test */
    public function it_can_addSubscriber()
    {
        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')
          ->with('POST', '/subscribers', ['name' => 'Sample Subscriber'])
          ->andReturn(new Response(200, [], json_encode(['id' => 'iU4s', 'name' => 'Sample Subscriber'])))
          ->once()
          ->getMock();

        $wh = new WebHooker($http);

        $subscriber = $wh->addSubscriber('Sample Subscriber');

        $this->assertInstanceOf(Subscriber::class, $subscriber);
        $this->assertEquals('Sample Subscriber', $subscriber->name);
        $this->assertEquals('iU4s', $subscriber->id);
    }

    // TODO: addSubscriber errors

    /** @test */
    public function it_returns_a_subscriber_by_id()
    {
        $wh = new WebHooker(m::mock(HttpClient::class));

        $subscriber = $wh->subscriber('Duh2');
        $this->assertInstanceOf(Subscriber::class, $subscriber);
        $this->assertEquals('Duh2', $subscriber->id);
    }

    /** @test */
    public function it_can_add_a_subscription_on_a_subscriber()
    {
        $tenantKey = 'account-1';
        $deliveryUrl = 'https://ffo.com/x';
        $format = 'application/json';
        $secretKey = 'thisisthesecretkey!!!';

        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')
          ->with('POST', '/subscribers/wij/subscriptions', [
            'tenant' => $tenantKey,
            'url' => $deliveryUrl,
            'format' => $format,
            'secret' => $secretKey,
          ])
          ->andReturn(new Response(200, [], json_encode([
            'id' => 'XD',
            'subscriber_id' => 'wij',
            'tenant' => $tenantKey,
            'format' => $format,
            'url' => $deliveryUrl,
          ])))
          ->once()
          ->getMock();

        $wh = new WebHooker($http);

        $subscription = $wh->subscriber('wij')->receive($tenantKey, $format, $deliveryUrl, $secretKey);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('XD', $subscription->id);
        $this->assertEquals('wij', $subscription->subscriberId);
        $this->assertEquals($tenantKey, $subscription->tenant);
        $this->assertEquals($format, $subscription->format);
        $this->assertEquals($deliveryUrl, $subscription->url);
        $this->assertFalse(property_exists($subscription, 'secret'));
    }

    /** @test */
    public function it_can_add_a_subscription_with_a_receiveJson_helper_method()
    {
        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')
          ->with('POST', '/subscribers/foo/subscriptions', [
              'tenant' => 'x',
              'url' => 'y',
              'format' => 'application/json',
              'secret' => 'blah',
          ])->andReturn($this->aSubscriptionHttpResponse())->once()->getMock();

        $wh = new WebHooker($http);

        $wh->subscriber('foo')->receiveJson('x', 'y', 'blah');
    }

    /** @test */
    public function it_can_add_a_subscription_with_a_receiveXml_helper_method()
    {
        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')
          ->with('POST', '/subscribers/foo/subscriptions', [
            'tenant' => 'x',
            'url' => 'y',
            'format' => 'application/xml',
            'secret' => 'blah',
          ])->andReturn($this->aSubscriptionHttpResponse())->once()->getMock();

        $wh = new WebHooker($http);

        $wh->subscriber('foo')->receiveXml('x', 'y', 'blah');
    }

    // TODO: receive errors

    /** @test */
    public function it_can_send_a_json_message_as_an_array()
    {
        $id = 'ew384';
        $tenant = 'account-1';
        $type = 'something.happened';
        $formats = ['application/json'];
        $recipientsBeingDeliveredTo = 4;

        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')
          ->with('POST', '/messages', [
            'tenant' => $tenant,
            'type' => $type,
            'payload' => [
              'application/json' => '{"foo":["bar","baz"]}',
            ],
          ])
          ->andReturn(new Response(200, [], json_encode([
            'id' => $id,
            'tenant' => $tenant,
            'type' => $type,
            'formats' => $formats,
            'recipients' => $recipientsBeingDeliveredTo,
          ])))
          ->once()
          ->getMock();

        $wh = new WebHooker($http);

        $message = $wh->notify($tenant, $type)->send([
          'foo' => ['bar', 'baz'],
        ]);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($id, $message->id);
        $this->assertEquals($tenant, $message->tenant);
        $this->assertEquals($type, $message->type);
        $this->assertEquals($formats, $message->formats);
        $this->assertEquals($recipientsBeingDeliveredTo, $message->recipients);
    }

    /** @test */
    public function it_can_send_a_message_as_already_encoded_json()
    {
        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/json' => '{"foo":2}',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        $wh = new WebHooker($http);

        $wh->notify('x', 'y')->send(json_encode(['foo' => 2]));
    }

    /** @test */
    public function it_can_send_xml_too()
    {
        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/xml' => '<hello>world</hello>',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        $wh = new WebHooker($http);

        $wh->notify('x', 'y')->xml('<hello>world</hello>')->send();
    }

    /** @test */
    public function it_can_send_json_and_xml()
    {
        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/xml' => '<hello>world</hello>',
              'application/json' => '{"foo":"bar"}',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        $wh = new WebHooker($http);

        $wh->notify('x', 'y')->xml('<hello>world</hello>')->json(['foo' => 'bar'])->send();
    }

    /** @test */
    public function it_adding_json_twice_prefers_the_latest_one()
    {
        $http = m::mock(HttpClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/json' => '{"hello":"world"}',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        $wh = new WebHooker($http);

        $wh->notify('x', 'y')->json(['foo' => 'bar'])->send(['hello' => 'world']);
    }

    private function aSubscriptionHttpResponse()
    {
        return new Response(200, [], json_encode([
          'id' => 'XD',
          'subscriber_id' => 'wij',
          'tenant' => 'foo',
          'format' => 'blfo',
          'url' => 'efij',
        ]));
    }

    private function aMessageHttpResponse()
    {
        return new Response(200, [], json_encode([
          'id' => '348de',
          'tenant' => 'ew',
          'type' => 'enwi',
          'formats' => ['application/json'],
          'recipients' => 1,
        ]));
    }
}