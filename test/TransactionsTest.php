<?php
use PHPUnit\Framework\TestCase;

class TransactionsTest extends TestCase
{
    public function testCreate()
    {
        $stub = $this->getMockBuilder('PomeloPayConnect\Client')->disableOriginalConstructor()->getMock();
        $stub->method('post')->willReturn('foo');

        $transactions = new \PomeloPayConnect\Transactions($stub);
        $this->assertEquals('foo', $transactions->create(['foo' => 'bar', 'amount' => 123, 'currency' => 'EUR']));

    }
}