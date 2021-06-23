<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test;

use LogicException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\WordPress\Http\HandlerInterface;
use RebelCode\WordPress\Http\Middleware;

/** @covers \RebelCode\WordPress\Http\Middleware */
class MiddlewareTest extends TestCase
{
    public function testImplementsHandlerInterface()
    {
        $middleware = $this->getMockBuilder(Middleware::class)
                           ->disableOriginalConstructor()
                           ->getMockForAbstractClass();

        self::assertInstanceOf(HandlerInterface::class, $middleware);
    }

    public function testNext()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(HandlerInterface::class);
        $handler->expects($this->once())->method('handle')->with($request)->willReturn($response);

        $middleware = new class($handler) extends Middleware {
            /** @inheritDoc */
            public function handle(RequestInterface $request): ResponseInterface
            {
                return $this->next($request);
            }
        };

        $actual = $middleware->handle($request);

        $this->assertSame($response, $actual);
    }

    public function testNextNullHandler()
    {
        $this->expectException(LogicException::class);

        $middleware = new class() extends Middleware {
            /* Does not call parent constructor to set the next handler */
            public function __construct() { }

            /** @inheritDoc */
            public function handle(RequestInterface $request): ResponseInterface
            {
                return $this->next($request);
            }
        };

        $request = $this->createMock(RequestInterface::class);

        $middleware->handle($request);
    }
}
