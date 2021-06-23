<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http\Test;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RebelCode\Psr7\Request;
use RebelCode\Psr7\Uri;
use RebelCode\WordPress\Http\HandlerInterface;
use RebelCode\WordPress\Http\WpHandler;
use Requests_Utility_CaseInsensitiveDictionary;
use WP_Mock;

/** @covers \RebelCode\WordPress\Http\WpHandler */
class WpHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        WP_Mock::setUp();
    }

    protected function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    public function testImplementsHandlerInterface()
    {
        self::assertInstanceOf(HandlerInterface::class, new WpHandler());
    }

    public function testHandle()
    {
        // Request data
        {
            $uri = 'http://example.org/foo/bar';
            $method = 'POST';
            $httpVer = '1.1';
            $requestBody = 'Some request body content here';
            $psr7RequestHeaders = [
                'Foo' => ['Bar', 'Baz'],
                'Qux' => ['Quux'],
            ];
            $wpRequestHeaders = [
                'Host' => 'example.org',
                'Foo' => 'Bar, Baz',
                'Qux' => 'Quux',
            ];

            $request = new Request($method, new Uri($uri), $psr7RequestHeaders, $requestBody, $httpVer);
        }

        // Response data
        {
            $statusCode = 201;
            $statusReason = 'Created';
            $responseHeaders = new Requests_Utility_CaseInsensitiveDictionary([
                'Sam' => 'Eggs, Ham',
                'Cat' => 'Hat',
            ]);
            $responseBody = 'Some response body content here';

            $responseData = [
                'response' => [
                    'code' => $statusCode,
                    'message' => $statusReason,
                    'headers' => $responseHeaders,
                    'body' => $responseBody,
                ],
            ];
        }

        // WP function mocks
        {
            WP_Mock::userFunction('wp_remote_request', [
                'times' => 1,
                'args' => [
                    $uri,
                    [
                        'method' => $method,
                        'httpversion' => $httpVer,
                        'headers' => $wpRequestHeaders,
                        'body' => $requestBody,
                    ],
                ],
                'return' => $responseData,
            ]);

            WP_Mock::userFunction('wp_remote_retrieve_response_code', [
                'times' => 1,
                'args' => [$responseData],
                'return' => $statusCode,
            ]);

            WP_Mock::userFunction('wp_remote_retrieve_response_message', [
                'times' => 1,
                'args' => [$responseData],
                'return' => $statusReason,
            ]);

            WP_Mock::userFunction('wp_remote_retrieve_headers', [
                'times' => 1,
                'args' => [$responseData],
                'return' => $responseHeaders,
            ]);

            WP_Mock::userFunction('wp_remote_retrieve_body', [
                'times' => 1,
                'args' => [$responseData],
                'return' => $responseBody,
            ]);
        }

        $subject = new WpHandler();
        $response = $subject->handle($request);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals($statusCode, $response->getStatusCode());
        self::assertEquals($statusReason, $response->getReasonPhrase());
        self::assertEquals($httpVer, $response->getProtocolVersion());
        self::assertEquals($responseBody, $response->getBody()->getContents());
        self::assertEquals(['Eggs, Ham'], $response->getHeader('Sam'));
        self::assertEquals(['Hat'], $response->getHeader('Cat'));
    }
}

