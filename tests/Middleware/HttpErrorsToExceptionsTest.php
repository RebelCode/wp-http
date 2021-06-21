<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RebelCode\Psr7\Request;
use RebelCode\Psr7\Response;
use RebelCode\WordPress\Http\Exception\ClientErrorException;
use RebelCode\WordPress\Http\Exception\ServerErrorException;
use RebelCode\WordPress\Http\HandlerInterface;
use RebelCode\WordPress\Http\Middleware;

class HttpErrorsToExceptionsTest extends TestCase
{
    public function testExtendsMiddleware()
    {
        $handler = $this->createMock(HandlerInterface::class);

        self::assertInstanceOf(Middleware::class, new Middleware\HttpErrorsToExceptions($handler));
    }

    public function handleTestDataProvider()
    {
        return [
            '1xx response' => [100, null],
            '2xx response' => [200, null],
            '3xx response' => [300, null],
            '4xx response' => [400, ClientErrorException::class],
            '5xx response' => [500, ServerErrorException::class],
        ];
    }

    /** @dataProvider handleTestDataProvider */
    public function testHandle($code, $exception)
    {
        if ($exception) {
            $this->expectException($exception);
        }

        $handler = $this->createMock(HandlerInterface::class);
        $request = new Request('GET', 'http://example.org/test');
        $response = new Response($code);

        $handler->expects($this->once())->method('handle')->with($request)->willReturn($response);

        $subject = new Middleware\HttpErrorsToExceptions($handler);
        $result = $subject->handle($request);

        if ($exception === null) {
            self::assertSame($response, $result);
        }
    }
}
