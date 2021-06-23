<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\Psr7\Request;
use RebelCode\Psr7\Uri;
use RebelCode\WordPress\Http\HandlerInterface;
use RebelCode\WordPress\Http\WpClient;

/** @covers \RebelCode\WordPress\Http\WpClient */
class WpClientTest extends TestCase
{
    public function testImplementsClientInterface()
    {
        $handler = $this->createMock(HandlerInterface::class);
        $subject = new WpClient($handler);

        self::assertInstanceOf(ClientInterface::class, $subject);
    }

    public function testSendRequest()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(HandlerInterface::class);
        $handler->expects($this->atLeastOnce())->method('handle')->with($request)->willReturn($response);

        $subject = new WpClient($handler);

        $actual = $subject->sendRequest($request);

        self::assertSame($response, $actual);
    }

    public function testPrepareRequest()
    {
        $baseUri = new Uri('http://example.org/api/');
        $requestUri = new Uri('foo/bar');

        $request = new Request('GET', $requestUri);
        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(HandlerInterface::class);
        $handler->expects($this->atLeastOnce())->method('handle')->willReturnCallback(
            function ($newRequest) use ($request, $response) {
                self::assertNotSame($request, $newRequest);
                self::assertEquals('http://example.org/api/foo/bar', (string) $newRequest->getUri());

                return $response;
            }
        );

        $subject = new WpClient($handler, $baseUri);
        $actual = $subject->sendRequest($request);

        self::assertSame($response, $actual);
    }

    public function testCreateDefault()
    {
        $result = WpClient::createDefault();

        self::assertInstanceOf(WpClient::class, $result);
    }
}
