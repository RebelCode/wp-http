<?php

declare(strict_types=1);

namespace RebelCode\WordPress\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HandlerInterface
{
    /**
     * Handles a request and creates a response.
     *
     * @param RequestInterface $request The request to handle.
     *
     * @return ResponseInterface The response for the handled request.
     */
    public function handle(RequestInterface $request): ResponseInterface;
}
