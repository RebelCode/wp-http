<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use RebelCode\WordPress\Http\Exception\NetworkException;
use RebelCode\WordPress\Http\Exception\RequestException;

/** @covers \RebelCode\WordPress\Http\Exception\NetworkException */
class NetworkExceptionTest extends TestCase
{
    public function testImplementsNetworkExceptionInterface()
    {
        $request = $this->createMock(RequestInterface::class);
        $subject = new NetworkException($request);

        self::assertInstanceOf(NetworkExceptionInterface::class, $subject);
    }

    public function testExtendsRequestException()
    {
        $request = $this->createMock(RequestInterface::class);
        $subject = new NetworkException($request);

        self::assertInstanceOf(RequestException::class, $subject);
    }
}
