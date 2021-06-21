# RebelCode - WP HTTP Client

A WordPress HTTP client that is compliant with the [PSR-18 HTTP client][psr18] standard.

## Installation

```
composer require rebelcode/wp-http
```

## Usage

Example of simple usage:

```php
use RebelCode\WordPress\Http\WpClient;

$client = WpClient::createDefault('https://base-url.com/api', [
    'timeout' => 30,
    'redirection' => 3,
]);
```

Example of customized client:

```php
use RebelCode\WordPress\Http\HandlerStack;
use RebelCode\WordPress\Http\WpClient;
use RebelCode\WordPress\Http\WpHandler;

$wpHandler = new WpHandler([
    'timeout' => 30,
    'redirection' => 3,
]);

$handlerStack = new HandlerStack($wpHandler, [
    // put middlewares here
]);

// With no middlewares, just the main handler
$client = new WpClient($wpHandler, 'https://base-url.com/api');

// With middlewares, using the entire stack
$client = new WpClient($handlerStack, 'https://base-url.com/api');
```

## Credits

The design and architecture of this package is loosely based on [guzzle/guzzle] (v6.5).

[psr18]: https://www.php-fig.org/psr/psr-18/
[guzzle/guzzle]: https://github.com/guzzle/guzzle
