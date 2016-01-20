<?php

namespace WebHooker\Test;

use GuzzleHttp\ClientInterface;
use Mockery as m;
use GuzzleHttp\Client;
use WebHooker\Config;
use WebHooker\GuzzleHttpClient;
use WebHooker\HttpClient;

class GuzzleHttpClientTest extends TestCase
{
    /** @test */
    public function it_is_HttpClient()
    {
        $this->assertInstanceOf(HttpClient::class, new GuzzleHttpClient(new Client, new Config));
    }

    /** @test */
    public function it_passes_through_requests_encoded_as_json()
    {
        $guzzle = m::mock(ClientInterface::class)
          ->shouldReceive('request')
          ->with('POST', 'https://foo.com/blah', [
            'body' => '{"foo":"bar"}',
            'headers' => [
              'Content-Type' => 'application/json',
              'X-API-Key' => 'abc123',
            ],
            'http_errors' => true,
          ])
          ->andReturn('the response here')
          ->once()
          ->getMock();

        $client = new GuzzleHttpClient($guzzle, Config::make('abc123')->setDomain('https://foo.com'));

        $response = $client->send('POST', '/blah', ['foo' => 'bar']);

        $this->assertEquals('the response here', $response);
    }
}