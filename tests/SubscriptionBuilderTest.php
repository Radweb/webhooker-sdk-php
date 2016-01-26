<?php

namespace WebHooker\Test;

use GuzzleHttp\Psr7\Response;
use Mockery as m;
use WebHooker\ApiClient;
use WebHooker\Subscription;
use WebHooker\SubscriptionBuilder;

class SubscriptionBuilderTest extends TestCase
{
    /** @test */
    public function it_passes_through_details()
    {
        $format = 'application/blah';
        $tenantKey = 'account-1';
        $deliveryUrl = 'https://ffo.com/x';
        $secret = 'blahblah';

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
            ])
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => false,
                'legacy' => [],
            ])))
            ->once()
            ->getMock();

        $subscription = (new SubscriptionBuilder($api, 'wij', $format, $tenantKey, $deliveryUrl, $secret))->save();

        $expected = new Subscription('XD', 'wij', $tenantKey, $format, $deliveryUrl);

        $this->assertEquals($expected, $subscription);
    }

    /** @test */
    public function it_can_be_configured_with_basic_auth_details()
    {
        $format = 'application/blah';
        $tenantKey = 'account-1';
        $deliveryUrl = 'https://ffo.com/x';
        $secret = 'blahblah';

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
                'auth' => [
                    'username' => 'bob',
                    'password' => 'qwerty',
                ],
            ])
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => true,
                'legacy' => [],
            ])))
            ->once()
            ->getMock();

        $subscription = (new SubscriptionBuilder($api, 'wij', $format, $tenantKey, $deliveryUrl, $secret))->basicAuth('bob', 'qwerty')->save();

        $expected = new Subscription('XD', 'wij', $tenantKey, $format, $deliveryUrl);
        $expected->setUsesBasicAuth(true);

        $this->assertEquals($expected, $subscription);
    }

    /** @test */
    public function it_can_be_configured_with_legacy_mode_details()
    {
        $format = 'application/blah';
        $tenantKey = 'account-1';
        $deliveryUrl = 'https://ffo.com/x';
        $secret = 'blahblah';

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
                'legacy' => [
                    'payload' => 'p_reply',
                ],
            ])
            ->andReturn(new Response(200, [], json_encode([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => false,
                'legacy' => [
                    'payload' => 'p_reply',
                ],
            ])))
            ->once()
            ->getMock();

        $subscription = (new SubscriptionBuilder($api, 'wij', $format, $tenantKey, $deliveryUrl, $secret))->legacyPayload('p_reply')->save();

        $expected = new Subscription('XD', 'wij', $tenantKey, $format, $deliveryUrl);
        $expected->setLegacyPayload('p_reply');

        $this->assertEquals($expected, $subscription);
    }
}