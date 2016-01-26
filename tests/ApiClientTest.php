<?php

namespace WebHooker\Test;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use WebHooker\ApiClient;
use WebHooker\Config;
use WebHooker\Exceptions\ExpiredException;
use WebHooker\Exceptions\InvalidRequestException;
use WebHooker\Exceptions\NotFoundException;
use WebHooker\Exceptions\UnauthorisedRequestException;
use WebHooker\Exceptions\UnknownClientException;
use WebHooker\Exceptions\UnknownServerException;

class ApiClientTest extends TestCase
{
    /** @test */
    public function it_builds_request_with_config()
    {
        $http = new TestHttpClient([
            new Response(200, [], json_encode(['lorem' => ['ipsum', 'dolor']])),
        ]);

        $api = new ApiClient($http, Config::make('qwerty')->setDomain('https://foo.com'));

        $response = $api->send('POST', '/blah', ['foo' => 'bar']);

        $this->assertEquals(['lorem' => ['ipsum', 'dolor']], $response);

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

    /** @test */
    public function it_throws_on_401()
    {
        $this->setExpectedException(UnauthorisedRequestException::class);

        $this->runRequest(401);
    }

    /** @test */
    public function it_throws_on_400_with_validation_errors()
    {
        $details = [
            'name' => ['The name field is required.'],
        ];

        try {
            $this->runRequest(400, [
                'message' => 'Invalid Request',
                'details' => $details,
            ]);
        } catch (InvalidRequestException $e) {
            $this->assertEquals('Invalid Request', $e->getMessage());
            $this->assertEquals($details, $e->getDetails());

            return;
        }

        $this->fail('expected exception was not thrown');
    }

    /** @test */
    public function it_throws_on_400_without_details_if_malformed_body()
    {
        try {
            $this->runRequest(400);
        } catch (InvalidRequestException $e) {
            $this->assertEquals('Invalid Request', $e->getMessage());

            return;
        }

        $this->fail('expected exception was not thrown');
    }

    /** @test */
    public function it_throws_on_402_team_expired()
    {
        $this->setExpectedException(ExpiredException::class);

        $this->runRequest(402);
    }

    /** @test */
    public function it_throws_on_404()
    {
        $this->setExpectedException(NotFoundException::class);

        $this->runRequest(404);
    }

    /** @test */
    public function it_throws_on_other_4XX_eg_419()
    {
        $this->setExpectedException(UnknownClientException::class);

        $this->runRequest(419);
    }

    /** @test */
    public function it_throws_on_5XX()
    {
        $this->setExpectedException(UnknownServerException::class);

        $this->runRequest(500);
    }

    private function runRequest($status, $responseBody = [])
    {
        $http = new TestHttpClient([
            new Response($status, [], json_encode($responseBody)),
        ]);

        $api = new ApiClient($http, Config::make());

        $api->send('POST', '/blah');
    }
}
