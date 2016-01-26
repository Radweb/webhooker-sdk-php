<?php

namespace WebHooker\Test;

use WebHooker\Config;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_defaults_domain()
    {
        $this->assertEquals('https://api.webhooker.io', (new Config())->getDomain());
    }

    /** @test */
    public function it_can_set_custom_domain_for_testing()
    {
        $config = new Config();
        $this->assertEquals($config, $config->setDomain('foo'));
        $this->assertEquals('foo', $config->getDomain());
    }

    /** @test */
    public function it_has_null_api_key()
    {
        $this->assertNull((new Config())->getApiKey());
    }

    /** @test */
    public function it_can_set_api_key()
    {
        $config = new Config();
        $this->assertEquals($config, $config->setApiKey('bar'));
        $this->assertEquals('bar', $config->getApiKey());
    }

    /** @test */
    public function it_has_static_make_method_accepting_an_optional_api_key()
    {
        $config = Config::make();
        $this->assertNull($config->getApiKey());
        $this->assertEquals('https://api.webhooker.io', $config->getDomain());

        $this->assertEquals('123abc', Config::make('123abc')->getApiKey());
    }
}
