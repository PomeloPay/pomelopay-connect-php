<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testClientEndpoint()
    {
        $client = new PomeloPayConnect\Client('foo', 'bar');

        $this->assertEquals(
            $client::PRODUCTION_ENDPOINT,
            PHPUnit_Framework_Assert::readAttribute($client, "baseUrl")
        );

        $client = new PomeloPayConnect\Client('foo', 'bar', 'sandbox');

        $this->assertEquals(
            $client::SANDBOX_ENDPOINT,
            PHPUnit_Framework_Assert::readAttribute($client, "baseUrl")
        );

    }


    public function testPost()
    {
        $mock = new MockHandler([new Response(200, ['X-Foo' => 'Bar'], "{\"foo\":\"bar\"}")]);
        $container = [];
        $history = Middleware::history($container);
        $stack = HandlerStack::create($mock);
        $stack->push($history);
        $http_client = new Client(['handler' => $stack]);
        $client = new PomeloPayConnect\Client('foo' , 'bar');
        $client->setClient($http_client);

        $client->transactions->create([
            'foo' => 'bar',
            'amount' => 123,
            'currency' => 'EUR',
            'webhook' => 'https://foo.bar',
            'validForHours' => 3
        ]);

        foreach ($container as $transaction) {
            $body = json_decode($transaction['request']->getBody()->getContents(), true);
            $this->assertEquals($body['foo'], 'bar');
            $this->assertEquals($body['amount'], 123);
            $this->assertEquals($body['currency'], 'EUR');
            $this->assertEquals($body['webhook'], 'https://foo.bar');
            $this->assertEquals($body['signature'], '458776b3f175e2ae56f65acab1f685c7a1350d79');
            $this->assertEquals($body['apiVersion'], '2.0');
            $this->assertEquals($body['appVersion'], 'pomelopay-connect-php');
            $this->assertEquals($body['signMethod'], 'sha1');
            $this->assertNotEmpty($body['expires']);
            $this->assertEquals('POST', $transaction['request']->getMethod());
        }
    }
}
