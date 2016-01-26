<?php

namespace WebHooker\Test;

use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }
}
