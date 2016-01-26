<?php

namespace WebHooker\Test;

use Mockery as m;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use WebHooker\ApiClient;
use WebHooker\Config;

class ApiClientTest extends TestCase
{
    /** @test */
    public function it_builds_request_with_config()
    {
        $http = new TestHttpClient;
        $http->responses[] = 'xx';

        $api = new ApiClient($http, Config::make('qwerty')->setDomain('https://foo.com'));

        $this->assertEquals('xx', $api->send('POST', '/blah', ['foo' => 'bar']));

        $this->assertCount(1, $http->requests);

        /** @var RequestInterface $request */
        $request = $http->requests[0];

        $this->assertInstanceOf(RequestInterface::class, $request);

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://foo.com/blah', $request->getUri());
        $this->assertEquals([
            'Host' => ['foo.com'],
            'x-api-key' => ['qwerty'],
            'content-type' => ['application/json'],
        ], $request->getHeaders());
        $this->assertInstanceOf(StreamInterface::class, $request->getBody());
        $this->assertEquals('{"foo":"bar"}', (string) $request->getBody());
    }
}