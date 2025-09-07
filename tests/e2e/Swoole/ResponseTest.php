<?php

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Tests\E2E\Client;

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

    protected ?Client $client;

    public function testCanRespond(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/');
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testCanRespondWithChunck(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/chunked');
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testResponseWithCookie(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/cookie');
        $this->assertNotEmpty($response['headers']['set-cookie']);
        $this->assertEquals('Hello with cookie!', $response['body']);
    }

    public function testCanRespondWithRedirect(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/redirect');
        $this->assertEquals('Hello World!', $response['body']);
    }

    /**
     * @return array<string, mixed>
     */
    public function providerForwardProtocolHeader(): array
    {
        return [
            'http' => ['http'],
            'https' => ['https'],
            'ws' => ['ws'],
            'wss' => ['wss'],
        ];
    }

    /**
     * @dataProvider providerForwardProtocolHeader
     */
    public function testCanForwardProtocolHeader(string $protocol): void
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', [
            "x-forwarded-proto: {$protocol}",
        ]);
        $this->assertEquals($protocol, $responseinvalid['body']);
    }

    public function testCantForwardUnknownProtocolHeader(): void
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', [
            'x-forwarded-proto: randomjibberish',
        ]);
        $this->assertEquals('https', $responseinvalid['body']);
    }

    public function testRequestHeaders(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/headers', [
            'x-test-header: developers-are-awesome',
        ]);

        $body = \json_decode($response['body'], true);

        $this->assertEquals('developers-are-awesome', $body['headers']['x-test-header']);
    }

    public function testCookie(): void
    {
        // One cookie
        $cookie = 'cookie1=value1';
        $response = $this->client->call(Client::METHOD_GET, '/cookies', [ 'Cookie: ' . $cookie ]);
        $this->assertEquals(200, $response['headers']['status-code']);
        $this->assertEquals($cookie, $response['body']);

        // Two cookiees
        $cookie = 'cookie1=value1; cookie2=value2';
        $response = $this->client->call(Client::METHOD_GET, '/cookies', [ 'Cookie: ' . $cookie ]);
        $this->assertEquals(200, $response['headers']['status-code']);
        $this->assertEquals($cookie, $response['body']);

        // Two cookies without optional space
        $cookie = 'cookie1=value1;cookie2=value2';
        $response = $this->client->call(Client::METHOD_GET, '/cookies', [ 'Cookie: ' . $cookie ]);
        $this->assertEquals(200, $response['headers']['status-code']);
        $this->assertEquals($cookie, $response['body']);

        // Cookie with "=" in value
        $cookie = 'cookie1=value1=value2';
        $response = $this->client->call(Client::METHOD_GET, '/cookies', [ 'Cookie: ' . $cookie ]);
        $this->assertEquals(200, $response['headers']['status-code']);
        $this->assertEquals($cookie, $response['body']);

        // Case sensitivity for cookie names
        $cookie = 'cookie1=v1; Cookie1=v2';
        $response = $this->client->call(Client::METHOD_GET, '/cookies', [ 'Cookie: ' . $cookie ]);
        $this->assertEquals(200, $response['headers']['status-code']);
        $this->assertEquals($cookie, $response['body']);
    }

    public function testSetCookie(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/set-cookie');
        $this->assertEquals(200, $response['headers']['status-code']);
        $this->assertEquals('value1', $response['cookies']['key1']);
        $this->assertEquals('value2', $response['cookies']['key2']);
    }
}
