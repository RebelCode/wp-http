<?php

const VENDOR_DIR = __DIR__ . '/../vendor';
const WORDPRESS_DIR = VENDOR_DIR . '/johnpbloch/wordpress-core';

require_once VENDOR_DIR . '/autoload.php';

WP_Mock::bootstrap();

/*
 * Load the Requests HTTP library bundled with WordPress. WordPress 6.2 shipped Requests 2.0,
 * which moved the library under wp-includes/Requests/src and renamed the classes from the flat
 * Requests_* names to the WpOrg\Requests namespace. Support both layouts so the suite runs
 * against whichever WordPress version Composer resolves across the PHP matrix.
 */
$newAutoload = WORDPRESS_DIR . '/wp-includes/Requests/src/Autoload.php';

if (file_exists($newAutoload)) {
    require_once $newAutoload;
    WpOrg\Requests\Autoload::register();
} else {
    require_once WORDPRESS_DIR . '/wp-includes/Requests/Utility/CaseInsensitiveDictionary.php';

    // Pre-6.2 WordPress only defines the legacy class name; alias it to the modern one the tests use.
    if (!class_exists(WpOrg\Requests\Utility\CaseInsensitiveDictionary::class)) {
        class_alias(
            'Requests_Utility_CaseInsensitiveDictionary',
            WpOrg\Requests\Utility\CaseInsensitiveDictionary::class
        );
    }
}
