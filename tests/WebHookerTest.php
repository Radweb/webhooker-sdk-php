<?php

namespace WebHooker\Test;

use GuzzleHttp\Psr7\Response;
use Mockery as m;
use WebHooker\ApiClient;
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

    // TODO: addSubscriber errors

    /** @test */
    public function it_returns_a_subscriber_by_id()
    {
        $api = m::mock(ApiClient::class);

        $subscriber = (new WebHooker($api))->subscriber('Duh2');

        $this->assertEquals(new Subscriber($api, 'Duh2', null), $subscriber);
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

        $api = m::mock(ApiClient::class)
          ->shouldReceive('send')
          ->with('POST', '/messages', [
            'tenant' => $tenant,
            'type' => $type,
            'payload' => [
              'application/json' => '{"foo":["bar","baz"]}',
            ],
          ])
          ->andReturn([
            'id' => $id,
            'tenant' => $tenant,
            'type' => $type,
            'formats' => $formats,
            'recipients' => $recipientsBeingDeliveredTo,
          ])
          ->once()
          ->getMock();

        $message = (new WebHooker($api))->notify($tenant, $type)->send([
          'foo' => ['bar', 'baz'],
        ]);

        $expected = new Message($id, $tenant, $type, $formats, $recipientsBeingDeliveredTo);

        $this->assertEquals($expected, $message);
    }

    /** @test */
    public function it_can_send_a_message_as_already_encoded_json()
    {
        $api = m::mock(ApiClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/json' => '{"foo":2}',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        (new WebHooker($api))->notify('x', 'y')->send(json_encode(['foo' => 2]));
    }

    /** @test */
    public function it_can_send_xml_too()
    {
        $api = m::mock(ApiClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/xml' => '<hello>world</hello>',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        (new WebHooker($api))->notify('x', 'y')->xml('<hello>world</hello>')->send();
    }

    /** @test */
    public function it_can_send_json_and_xml()
    {
        $api = m::mock(ApiClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/xml' => '<hello>world</hello>',
              'application/json' => '{"foo":"bar"}',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        (new WebHooker($api))->notify('x', 'y')->xml('<hello>world</hello>')->json(['foo' => 'bar'])->send();
    }

    /** @test */
    public function it_adding_json_twice_prefers_the_latest_one()
    {
        $api = m::mock(ApiClient::class)
          ->shouldReceive('send')->with('POST', '/messages', [
            'tenant' => 'x',
            'type' => 'y',
            'payload' => [
              'application/json' => '{"hello":"world"}',
            ],
          ])->andReturn($this->aMessageHttpResponse())->once()->getMock();

        (new WebHooker($api))->notify('x', 'y')->json(['foo' => 'bar'])->send(['hello' => 'world']);
    }

    private function aMessageHttpResponse()
    {
        return [
          'id' => '348de',
          'tenant' => 'ew',
          'type' => 'enwi',
          'formats' => ['application/json'],
          'recipients' => 1,
        ];
    }
}