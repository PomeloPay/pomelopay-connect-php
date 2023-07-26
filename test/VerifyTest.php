<?php
use PHPUnit\Framework\TestCase;

class VerifyTest extends TestCase
{
    public function testValidateSignature()
    {
        $client = new PomeloPayConnect\Client('foo' , 'bar');
        $this->assertEquals((new \PomeloPayConnect\Crypt\Verify())->validateSignature(
            $client,
            '458776b3f175e2ae56f65acab1f685c7a1350d79',
            123,
            'EUR'), true);
    }
}
