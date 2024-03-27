<?php

namespace WebHooker\Test;

use Mockery as m;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown():void
    {
        m::close();
    }
}
