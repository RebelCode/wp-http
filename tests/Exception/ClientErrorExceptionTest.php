<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\WordPress\Http\Exception\BadResponseException;
use RebelCode\WordPress\Http\Exception\ClientErrorException;

/** @covers \RebelCode\WordPress\Http\Exception\ClientErrorException */
class ClientErrorExceptionTest extends TestCase
{
    public function testImplementsClientExceptionInterface()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 0]);
        $subject = new ClientErrorException($request, $response);

        self::assertInstanceOf(ClientExceptionInterface::class, $subject);
    }

    public function testExtendsBadResponseException()
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createConfiguredMock(ResponseInterface::class, ['getStatusCode' => 0]);
        $subject = new ClientErrorException($request, $response);

        self::assertInstanceOf(BadResponseException::class, $subject);
    }
}
