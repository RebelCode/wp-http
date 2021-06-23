# RebelCode - WP HTTP Client

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/rebelcode/wp-http/Continuous%20Integration)][github-ci]
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/rebelcode/wp-http)][packagist]
[![Packagist Version](https://img.shields.io/packagist/v/rebelcode/wp-http)][packagist]
[![Packagist License](https://img.shields.io/packagist/l/rebelcode/wp-http)][packagist]

A WordPress HTTP client that complies with the [PSR-7 HTTP Message][psr7] and [PSR-18 HTTP client][psr18] standards.

**Note:** This package was written for use in RebelCode's WordPress products only, as a means to mitigate conflicts with
other plugins; most notably, those that use [Guzzle][guzzle]. Feel free to use this package, but please be advised that
doing so can cause conflicts, which defeats the purpose of this package.

## Installation

**With Composer:**

```
composer require rebelcode/wp-http
```

**Without Composer:**

1. Copy the contents of `src` into the directory of your choice
2. Use an autoloader to map the `RebelCode\WordPress\Http` namespace to that directory
3. Consider using [Composer](https://getcomposer.org/) instead

## Usage

**Creating a client instance**

```php
use RebelCode\WordPress\Http\WpClient;
use RebelCode\WordPress\Http\WpHandler;
use RebelCode\WordPress\Http\HandlerStack;
use RebelCode\WordPress\Http\Middleware;

/*-----------------------------------------------------------------------------
 * Default configuration.
 * - Uses the `WpHandler`
 * - Uses the `HttpErrorsToExceptions` middleware
 * - Uses the `PrepareBody` middleware
 */
 
$client = WpClient::createDefault('https://base-url.com/api', [
    'timeout' => 30,
    'redirection' => 3,
]);

/*-----------------------------------------------------------------------------
 * Custom configuration with middleware:
 * - Create the `WpHandler`
 * - Create a `HandlerStack` with the handler and middleware factories
 * - Create the `WpClient` and pass the stack
 */
 
$wpHandler = new WpHandler([
    'timeout' => 30,
    'redirection' => 3,
]);

$handlerStack = new HandlerStack($wpHandler, [
    Middleware::factory(Middleware\PrepareBody::class)
]);

$client = new WpClient($handlerStack, 'https://base-url.com/api');

/*-----------------------------------------------------------------------------
 * For a zero-middleware configuration, you can simply pass the base handler
 */

$client = new WpClient($wpHandler, 'https://base-url.com/api');
```

**Sending Requests**

The `WpClient` implements the PSR-18 `ClientInterface`. Requests are dispatched using `sendRequest()` method:

```php
use RebelCode\WordPress\Http\WpHandler;
use RebelCode\Psr7\Request;

$client = new WpClient(new WpHandler(), 'https://base-url.com/api');

$request = new Request('GET', '/users');
$response = $client->sendRequest($request);
```

## Architecture

The design and architecture of this package is loosely based on [Guzzle][guzzle].

The [`WpClient`][client] class does not actually use the WordPress HTTP API to send requests. Rather, it delegates the
handling of the request to a [`HandlerInterface`][handler] instance. The only thing the client is directly responsible
for is resolving relative request URIs using a base URI (if one is given to the client during construction).

Handlers are objects that take a `RequestInterface` instance and return a `ResponseInterface` instance. The
[`WpHandler`][wp-handler], for example, transforms the request into the array of arguments required by the
[`wp_remote_request()`][wp-remote-request] function, calls the function, then transforms the returned value into a
`ResponseInterface` instance.

## Middleware

Middlewares are a special type of `HandlerInterface`: they take a `RequestInterface` and return a `ResponseInterface`.

The key difference is that middlewares do not actually dispatch the request. Instead, they receive a `HandlerInterface`
instance during construction and delegate to it by calling `$this->next($request)`.

This allows for multiple middlewares to be chained together, such that the first middleware is given the second
middleware, which in turn is given the third middleware, and so on. The last middleware is then given the base handler,
typically the `WpClient` instance. This chaining is implemented in the [`HandlerStack`][handler-stack], which is also
a `HandlerInterface` implementation.

---

Middleware classes may accept additional constructor arguments, as long as a handler argument is accepted and is passed
to the parent constructor.

**Example implementation of a middleware**

```php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RebelCode\WordPress\Http\Middleware;

class MyMiddleware extends Middleware {
    public function __construct(HandlerInterface $handler, $arg1, $arg2) {
        parent::__construct($handler);
        // ...
    }

    /** @inheritDoc*/
    public function handle(RequestInterface $request) : ResponseInterface{
        // Do something with the request
        $newRequest = $request->withHeader('X-Foo', 'Bar');
        
        // Delegate to the next handler
        $response = $this->next($newRequest);
        
        // Do something with the response and return it
        return $response->withHeader('X-Baz', 'Qux');
    }
}
```

The middleware can then be given to the `HandlerStack` using a factory function that takes a `HandlerInterface`
instance and returns the middleware instance.

```php
$stack = new HandlerStack($baseHandler, [
    function ($handler) {
        return new MyMiddleware($handler, $arg1, $arg2);
    }
]);
```

If the first argument of the middleware constructor is the handler, the `Middleware::factory()` helper function can be
utilized to reduce boilerplate code. Additional constructor arguments can be passed as the second argument, in an array.

```php
$stack = new HandlerStack($baseHandler, [
    Middleware::factory(MyMiddleware::class, [$arg1, $arg2]),
]);
```

[github-ci]: https://github.com/RebelCode/wp-http/actions/workflows/continuous-integration.yml
[packagist]: https://packagist.org/packages/rebelcode/wp-http
[psr7]: https://www.php-fig.org/psr/psr-7/
[psr18]: https://www.php-fig.org/psr/psr-18/
[guzzle]: https://github.com/guzzle/guzzle
[handler]: src/HandlerInterface.php
[wp-handler]: src/WpHandler.php
[handler-stack]: src/HandlerStack.php
[client]: src/WpClient.php
[wp-remote-request]: https://developer.wordpress.org/reference/functions/wp_remote_request/
