<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\WordPress\Http\Exception\RequestException;
use RebelCode\WordPress\Http\Exception\ResponseException;

class ResponseExceptionTest extends TestCase
{
    public function testImplementsClientExceptionInterface()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 0]);
        $subject = new ResponseException($request, $response);

        self::assertInstanceOf(ClientExceptionInterface::class, $subject);
    }

    public function testExtendsRequestException()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 0]);
        $subject = new ResponseException($request, $response);

        self::assertInstanceOf(RequestException::class, $subject);
    }

    public function testGetResponse()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 0]);
        $subject = new ResponseException($request, $response);

        self::assertSame($response, $subject->getResponse());
    }

    public function testGetCode()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 403]);
        $subject = new ResponseException($request, $response);

        self::assertEquals(403, $subject->getCode());
    }
}
