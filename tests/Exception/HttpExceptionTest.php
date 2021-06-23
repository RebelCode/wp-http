<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use RebelCode\WordPress\Http\Exception\HttpException;
use RuntimeException;

/** @covers \RebelCode\WordPress\Http\Exception\HttpException */
class HttpExceptionTest extends TestCase
{
    public function testImplementsClientExceptionInterface()
    {
        self::assertInstanceOf(ClientExceptionInterface::class, new HttpException());
    }

    public function testExtendsRuntimeException()
    {
        self::assertInstanceOf(RuntimeException::class, new HttpException());
    }
}
