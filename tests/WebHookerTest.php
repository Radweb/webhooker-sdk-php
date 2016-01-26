<?php

namespace WebHooker\Test;

use GuzzleHttp\Psr7\Response;
use Mockery as m;
use WebHooker\HttpClient;
use WebHooker\Message;
use WebHooker\SubscriptionBuilder;
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
        $http = m::mock(HttpClient::class);

        $wh = new WebHooker($http);

        $subscriber = $wh->subscriber('Duh2');

        $this->assertEquals(new Subscriber($http, 'Duh2', null), $subscriber);
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

        $message = (new WebHooker($http))->notify($tenant, $type)->send([
          'foo' => ['bar', 'baz'],
        ]);

        $expected = new Message($id, $tenant, $type, $formats, $recipientsBeingDeliveredTo);

        $this->assertEquals($expected, $message);
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

        (new WebHooker($http))->notify('x', 'y')->send(json_encode(['foo' => 2]));
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

        (new WebHooker($http))->notify('x', 'y')->xml('<hello>world</hello>')->send();
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

        (new WebHooker($http))->notify('x', 'y')->xml('<hello>world</hello>')->json(['foo' => 'bar'])->send();
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

        (new WebHooker($http))->notify('x', 'y')->json(['foo' => 'bar'])->send(['hello' => 'world']);
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