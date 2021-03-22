<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class ExpiresTest extends PHPUnit_Framework_TestCase
{
    public function testExpires()
    {
        $mockNow = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', '2021-01-01T12:12:12.000001Z');
        $expires = new \PomeloPayConnect\Options\Expires($mockNow);
        $this->assertEquals('2021-01-01T15:12:12.000001Z', $expires->getExpiryDateAsIsoString(3));
    }

    public function testExpiresWithServerTimezone()
    {
        $mockNow = DateTime::createFromFormat('Y-m-d H:i:s O', '2021-01-01 13:09:44 +0400');
        $expires = new \PomeloPayConnect\Options\Expires($mockNow);
        $this->assertEquals('2021-01-01T13:09:44.000000Z', $expires->getExpiryDateAsIsoString(4));
    }

    public function testExpiresWithServertherTimezone()
    {
        $mockNow = DateTime::createFromFormat('Y-m-d H:i:s O', '2021-01-01 13:09:44 +0200');
        $expires = new \PomeloPayConnect\Options\Expires($mockNow);
        $this->assertEquals('2021-01-01T15:09:44.000000Z', $expires->getExpiryDateAsIsoString(4));
    }
}
