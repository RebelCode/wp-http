<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RebelCode\WordPress\Http\Exception\HttpException;
use RebelCode\WordPress\Http\Exception\RequestException;
use Throwable;

/** @covers \RebelCode\WordPress\Http\Exception\RequestException */
class RequestExceptionTest extends TestCase
{
    public function testImplementsClientExceptionInterface()
    {
        $request = $this->createMock(RequestInterface::class);
        $subject = new RequestException($request);

        self::assertInstanceOf(ClientExceptionInterface::class, $subject);
    }

    public function testExtendsHttpException()
    {
        $request = $this->createMock(RequestInterface::class);
        $subject = new RequestException($request);

        self::assertInstanceOf(HttpException::class, $subject);
    }

    public function testGetRequest()
    {
        $request = $this->createMock(RequestInterface::class);
        $subject = new RequestException($request);

        self::assertSame($request, $subject->getRequest());
    }

    public function testParentConstructor()
    {
        $request = $this->createMock(RequestInterface::class);
        $message = 'Foo bar baz';
        $code = 9;
        $previous = $this->createMock(Throwable::class);

        $subject = new RequestException($request, $message, $code, $previous);

        self::assertEquals($message, $subject->getMessage());
        self::assertEquals($code, $subject->getCode());
        self::assertSame($previous, $subject->getPrevious());
    }
}
