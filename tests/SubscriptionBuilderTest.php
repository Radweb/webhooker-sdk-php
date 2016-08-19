<?php

namespace WebHooker\Test;

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
        $events = [];

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
                'events' => $events,
            ])
            ->andReturn([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => false,
                'legacy' => [],
            ])
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
        $events = [];

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
                'events' => $events,
                'auth' => [
                    'username' => 'bob',
                    'password' => 'qwerty',
                ],
            ])
            ->andReturn([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => true,
                'legacy' => [],
            ])
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
        $events = [];

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
                'events' => $events,
                'legacy' => [
                    'payload' => 'p_reply',
                ],
            ])
            ->andReturn([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => false,
                'legacy' => [
                    'payload' => 'p_reply',
                ],
            ])
            ->once()
            ->getMock();

        $subscription = (new SubscriptionBuilder($api, 'wij', $format, $tenantKey, $deliveryUrl, $secret))->legacyPayload('p_reply')->save();

        $expected = new Subscription('XD', 'wij', $tenantKey, $format, $deliveryUrl);
        $expected->setLegacyPayload('p_reply');

        $this->assertEquals($expected, $subscription);
    }

    /** @test */
    public function it_can_be_configured_with_basic_auth_details_and_events()
    {
        $format = 'application/blah';
        $tenantKey = 'account-1';
        $deliveryUrl = 'https://ffo.com/x';
        $secret = 'blahblah';
        $events = ['inspection.completed'];

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
                'events' => $events,
                'auth' => [
                    'username' => 'bob',
                    'password' => 'qwerty',
                ],
            ])
            ->andReturn([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => true,
                'legacy' => [],
            ])
            ->once()
            ->getMock();

        $subscription = (new SubscriptionBuilder($api, 'wij', $format, $tenantKey, $deliveryUrl, $secret))->onlyEvents($events)->basicAuth('bob', 'qwerty')->save();

        $expected = new Subscription('XD', 'wij', $tenantKey, $format, $deliveryUrl);
        $expected->setUsesBasicAuth(true);

        $this->assertEquals($expected, $subscription);
    }

    /** @test */
    public function it_can_be_configured_with_legacy_mode_details_and_events()
    {
        $format = 'application/blah';
        $tenantKey = 'account-1';
        $deliveryUrl = 'https://ffo.com/x';
        $secret = 'blahblah';
        $events = ['inspection.completed'];

        $api = m::mock(ApiClient::class)
            ->shouldReceive('send')
            ->with('POST', '/subscribers/wij/subscriptions', [
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'secret' => $secret,
                'events' => $events,
                'legacy' => [
                    'payload' => 'p_reply',
                ],
            ])
            ->andReturn([
                'id' => 'XD',
                'subscriber_id' => 'wij',
                'format' => $format,
                'tenant' => $tenantKey,
                'url' => $deliveryUrl,
                'uses_basic_auth' => false,
                'legacy' => [
                    'payload' => 'p_reply',
                ],
            ])
            ->once()
            ->getMock();

        $subscription = (new SubscriptionBuilder($api, 'wij', $format, $tenantKey, $deliveryUrl, $secret))->onlyEvents($events)->legacyPayload('p_reply')->save();

        $expected = new Subscription('XD', 'wij', $tenantKey, $format, $deliveryUrl);
        $expected->setLegacyPayload('p_reply');

        $this->assertEquals($expected, $subscription);
    }
}
