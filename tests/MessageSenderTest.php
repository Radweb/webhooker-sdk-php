<?php

namespace WebHooker\Test;

use Mockery as m;
use WebHooker\ApiClient;
use WebHooker\Message;
use WebHooker\MessageSender;

class MessageSenderTest extends TestCase
{
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

        $message = (new MessageSender($api, $tenant, $type))->send([
            'foo' => ['bar', 'baz'],
        ]);

        $expected = new Message($id, $tenant, $type, $formats, $recipientsBeingDeliveredTo);

        $this->assertEquals($expected, $message);
    }

    /** @test */
    public function it_can_send_a_message_as_already_encoded_json()
    {
        $api = $this->createMock(ApiClient::class);
        $api->expects($this->once())
            ->method('send')
            ->with('POST', '/messages', [
                'tenant' => 'x',
                'type' => 'y',
                'payload' => [
                    'application/json' => '{"foo":2}',
                ],
            ])
            ->willReturn($this->aMessageHttpResponse());

        (new MessageSender($api, 'x', 'y'))->send(json_encode(['foo' => 2]));
    }

    /** @test */
    public function it_can_send_xml_too()
    {
        $api = $this->createMock(ApiClient::class);

        $api->expects($this->once())
            ->method('send')
            ->with('POST', '/messages', [
                'tenant' => 'x',
                'type' => 'y',
                'payload' => [
                    'application/xml' => '<hello>world</hello>',
                ],
            ])
            ->willReturn($this->aMessageHttpResponse());

        (new MessageSender($api, 'x', 'y'))->xml('<hello>world</hello>')->send();
    }

    /** @test */
    public function it_can_send_json_and_xml()
    {
        $api = $this->createMock(ApiClient::class);

        $api->expects($this->once())
            ->method('send')
            ->with('POST', '/messages', [
                'tenant' => 'x',
                'type' => 'y',
                'payload' => [
                    'application/xml' => '<hello>world</hello>',
                    'application/json' => '{"foo":"bar"}',
                ],
            ])
            ->willReturn($this->aMessageHttpResponse());

        (new MessageSender($api, 'x', 'y'))->xml('<hello>world</hello>')->json(['foo' => 'bar'])->send();
    }

    /** @test */
    public function it_adding_json_twice_prefers_the_latest_one()
    {
        $api = $this->createMock(ApiClient::class);

        $api->expects($this->once())
            ->method('send')
            ->with('POST', '/messages', [
                'tenant' => 'x',
                'type' => 'y',
                'payload' => [
                    'application/json' => '{"hello":"world"}',
                ],
            ])
            ->willReturn($this->aMessageHttpResponse());

        (new MessageSender($api, 'x', 'y'))->json(['foo' => 'bar'])->send(['hello' => 'world']);
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
