<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RebelCode\WordPress\Http\HandlerInterface;
use RebelCode\WordPress\Http\HandlerStack;
use RebelCode\WordPress\Http\Middleware;
use RebelCode\WordPress\Http\Test\Helpers\MockFunction;

/** @covers \RebelCode\WordPress\Http\HandlerStack */
class HandlerStackTest extends TestCase
{
    public function testResolveStackAppliesMiddlewares()
    {
        $request = $this->createMock(RequestInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        // Middleware mocks
        {
            $m1 = $this->createMock(Middleware::class);
            $m2 = $this->createMock(Middleware::class);
            $m3 = $this->createMock(Middleware::class);

            $f1 = $this->createMock(MockFunction::class);
            $f1->expects($this->atLeastOnce())->method('__invoke')->with($m2)->willReturn($m1);

            $f2 = $this->createMock(MockFunction::class);
            $f2->expects($this->atLeastOnce())->method('__invoke')->with($m3)->willReturn($m2);

            $f3 = $this->createMock(MockFunction::class);
            $f3->expects($this->atLeastOnce())->method('__invoke')->with($handler)->willReturn($m3);
        }

        $subject = new HandlerStack($handler, [$f1, $f2, $f3]);
        $subject->handle($request);
    }

    public function testResolveStackUsesCache()
    {
        $request = $this->createMock(RequestInterface::class);
        $handler = $this->createMock(HandlerInterface::class);

        // Middleware mocks
        {
            $m1 = $this->createMock(Middleware::class);
            $m2 = $this->createMock(Middleware::class);
            $m3 = $this->createMock(Middleware::class);

            $f1 = $this->createMock(MockFunction::class);
            $f1->expects($this->once())->method('__invoke')->with($m2)->willReturn($m1);

            $f2 = $this->createMock(MockFunction::class);
            $f2->expects($this->once())->method('__invoke')->with($m3)->willReturn($m2);

            $f3 = $this->createMock(MockFunction::class);
            $f3->expects($this->once())->method('__invoke')->with($handler)->willReturn($m3);
        }

        $subject = new HandlerStack($handler, [$f1, $f2, $f3]);

        // The cache should be used for the 2nd and 3rd calls
        $subject->handle($request);
        $subject->handle($request);
        $subject->handle($request);
    }

    public function testCreateDefault()
    {
        $result = HandlerStack::createDefault();

        self::assertInstanceOf(HandlerStack::class, $result);
    }
}
