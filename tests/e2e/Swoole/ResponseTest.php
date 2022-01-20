<?php

namespace Utopia\Tests;

use Tests\E2E\Client;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function setUp(): void
    {
        $this->client = new Client();
    }

    public function tearDown(): void
    {
        $this->client = null;
    }
    
    /**
     * @var Client $client
     */
    protected $client;

    public function testResponse()
    {
        $response = $this->client->call(Client::METHOD_GET, '/');
        $this->assertEquals('Hello World!', $response['body']);

    }

    public function testChunkResponse()
    {
        $response = $this->client->call(Client::METHOD_GET, '/chunked');
        $this->assertEquals('Hello World!', $response['body']);

    }

    public function testRedirect()
    {
        $response = $this->client->call(Client::METHOD_GET, '/redirect');
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testProtocolFilterFail()
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', [
            'x-forwarded-proto: randomjibberish'
        ]);
        $this->assertEquals('https', $responseinvalid['body']);
    }

    public function testProtocolFilterHTTP()
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', array(
            'x-forwarded-proto: http'
        ));
        $this->assertEquals('http', $responseinvalid['body']);
    }

    public function testProtocolFilterHTTPS()
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', array(
            'x-forwarded-proto: https'
        ));
        $this->assertEquals('https', $responseinvalid['body']);
    }


    public function testProtocolFilterWS()
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', array(
            'x-forwarded-proto: ws'
        ));
        $this->assertEquals('ws', $responseinvalid['body']);
    }

    public function testProtocolFilterWSS()
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', array(
            'x-forwarded-proto: wss'
        ));
        $this->assertEquals('wss', $responseinvalid['body']);
    }
}