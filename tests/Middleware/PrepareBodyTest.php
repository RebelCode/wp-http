<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RebelCode\Psr7\Request;
use RebelCode\WordPress\Http\HandlerInterface;
use RebelCode\WordPress\Http\Middleware;

/** @covers \RebelCode\WordPress\Http\Middleware\PrepareBody */
class PrepareBodyTest extends TestCase
{
    public function testExtendsMiddleware()
    {
        $handler = $this->createMock(HandlerInterface::class);

        self::assertInstanceOf(Middleware::class, new Middleware\HttpErrorsToExceptions($handler));
    }

    public function testHandleEmptyRequest()
    {
        $request = $this->createConfiguredMock(RequestInterface::class, [
            'getBody' => $this->createConfiguredMock(StreamInterface::class, [
                'getSize' => 0,
            ]),
        ]);

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        $handler->expects($this->once())->method('handle')->with($request)->willReturn($response);

        $subject = new Middleware\PrepareBody($handler);
        $result = $subject->handle($request);

        self::assertSame($response, $result);
    }

    public function testHandleContentTypeNoUriMetadata()
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getSize')->willReturn(10);
        $body->method('getMetadata')->with('uri')->willReturn(null);

        $request = new Request('GET', 'http://example.org', [
            'Content-Length' => [10],
        ], $body);

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        $handler->expects($this->once())->method('handle')->with($request)->willReturn($response);

        $subject = new Middleware\PrepareBody($handler);
        $result = $subject->handle($request);

        self::assertSame($response, $result);
    }

    public function testHandleAddContentType()
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getMetadata')->with('uri')->willReturn('my-file.txt');

        $request = new Request('GET', 'http://example.org', [
            'Content-Length' => [10],
        ], $body);

        $newRequest = $request->withAddedHeader('Content-Type', 'text/plain');

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        $handler->expects($this->once())->method('handle')->with($newRequest)->willReturn($response);

        $subject = new Middleware\PrepareBody($handler);
        $result = $subject->handle($request);

        self::assertSame($response, $result);
    }

    public function testHandleAddContentLength()
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getSize')->willReturn(10);

        $request = new Request('GET', 'http://example.org', [
            'Content-Type' => ['text/plain'],
        ], $body);

        $newRequest = $request->withAddedHeader('Content-Length', 10);

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        $handler->expects($this->once())->method('handle')->with($newRequest)->willReturn($response);

        $subject = new Middleware\PrepareBody($handler);
        $result = $subject->handle($request);

        self::assertSame($response, $result);
    }

    public function testHandleAddTransferEncoding()
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getSize')->willReturn(null);

        $request = new Request('GET', 'http://example.org', [
            'Content-Type' => ['text/plain'],
        ], $body);

        $newRequest = $request->withAddedHeader('Transfer-Encoding', 'chunked');

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        $handler->expects($this->once())->method('handle')->with($newRequest)->willReturn($response);

        $subject = new Middleware\PrepareBody($handler);
        $result = $subject->handle($request);

        self::assertSame($response, $result);
    }

    public function testHandleAddContentTypeAndContentLength()
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getMetadata')->with('uri')->willReturn('some-file.xml');
        $body->method('getSize')->willReturn(100);

        $request = new Request('GET', 'http://example.org', [], $body);

        $newRequest = $request->withAddedHeader('Content-Type', 'application/xml')
                              ->withAddedHeader('Content-Length', 100);

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        $handler->expects($this->once())->method('handle')->with($newRequest)->willReturn($response);

        $subject = new Middleware\PrepareBody($handler);
        $result = $subject->handle($request);

        self::assertSame($response, $result);
    }

    public function testHandleAddContentTypeAndTransferEncoding()
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('getMetadata')->with('uri')->willReturn('some-file.xml');
        $body->method('getSize')->willReturn(null);

        $request = new Request('GET', 'http://example.org', [], $body);

        $newRequest = $request->withAddedHeader('Content-Type', 'application/xml')
                              ->withAddedHeader('Transfer-Encoding', 'chunked');

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        $handler->expects($this->once())->method('handle')->with($newRequest)->willReturn($response);

        $subject = new Middleware\PrepareBody($handler);
        $result = $subject->handle($request);

        self::assertSame($response, $result);
    }
}
