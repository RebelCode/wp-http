<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\Psr7\Request;
use RebelCode\Psr7\Response;
use RebelCode\WordPress\Http\Exception\BadResponseException;
use RebelCode\WordPress\Http\Exception\ClientErrorException;
use RebelCode\WordPress\Http\Exception\RequestException;
use RebelCode\WordPress\Http\Exception\ServerErrorException;

/** @covers \RebelCode\WordPress\Http\Exception\BadResponseException */
class BadResponseExceptionTest extends TestCase
{
    public function testImplementsClientExceptionInterface()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 0]);
        $subject = new BadResponseException($request, $response);

        self::assertInstanceOf(ClientExceptionInterface::class, $subject);
    }

    public function testExtendsRequestException()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 0]);
        $subject = new BadResponseException($request, $response);

        self::assertInstanceOf(RequestException::class, $subject);
    }

    public function testCreate4xx()
    {
        $method = 'PUT';
        $code = 403;
        $reason = 'Forbidden';
        $url = 'http://example.org/test/put';
        $body = 'You are missing a permission';

        $request = new Request($method, $url);
        $response = new Response($code, [], $body, '1.1', $reason);
        $previous = new Exception();

        $result = BadResponseException::create($request, $response, $previous);

        self::assertInstanceOf(ClientErrorException::class, $result);
        self::assertSame($request, $result->getRequest());
        self::assertSame($response, $result->getResponse());
        self::assertEquals($code, $result->getCode());
        self::assertSame($previous, $result->getPrevious());
        self::assertStringStartsWith('Client error', $result->getMessage());
        self::assertStringContainsString("$method $url", $result->getMessage());
        self::assertStringContainsString("$code $reason", $result->getMessage());
        self::assertStringContainsString($body, $result->getMessage());
    }

    public function testCreate5xx()
    {
        $method = 'POST';
        $code = 500;
        $reason = 'Internal Server Error';
        $url = 'http://example.org/test/post';
        $body = 'Something went wrong';

        $request = new Request($method, $url);
        $response = new Response($code, [], $body, '1.1', $reason);
        $previous = new Exception();

        $result = BadResponseException::create($request, $response, $previous);

        self::assertInstanceOf(ServerErrorException::class, $result);
        self::assertSame($request, $result->getRequest());
        self::assertSame($response, $result->getResponse());
        self::assertEquals($code, $result->getCode());
        self::assertSame($previous, $result->getPrevious());
        self::assertStringStartsWith('Server error', $result->getMessage());
        self::assertStringContainsString("$method $url", $result->getMessage());
        self::assertStringContainsString("$code $reason", $result->getMessage());
        self::assertStringContainsString($body, $result->getMessage());
    }

    public function testCreateMisc()
    {
        $method = 'DELETE';
        $code = 306;
        $reason = 'Unused';
        $url = 'http://example.org/test/delete';
        $body = 'Unknown things are happening';

        $request = new Request($method, $url);
        $response = new Response($code, [], $body, '1.1', $reason);
        $previous = new Exception();

        $result = BadResponseException::create($request, $response, $previous);

        self::assertInstanceOf(BadResponseException::class, $result);
        self::assertSame($request, $result->getRequest());
        self::assertSame($response, $result->getResponse());
        self::assertEquals($code, $result->getCode());
        self::assertSame($previous, $result->getPrevious());
        self::assertStringStartsWith('Unsuccessful request', $result->getMessage());
        self::assertStringContainsString("$method $url", $result->getMessage());
        self::assertStringContainsString("$code $reason", $result->getMessage());
        self::assertStringContainsString($body, $result->getMessage());
    }
}
