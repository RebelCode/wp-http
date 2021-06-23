<?php

const VENDOR_DIR = __DIR__ . '/../vendor';
const WORDPRESS_DIR = VENDOR_DIR . '/johnpbloch/wordpress-core';

require_once VENDOR_DIR . '/autoload.php';

WP_Mock::bootstrap();

if (!class_exists('Requests_Utility_CaseInsensitiveDictionary')) {
    require WORDPRESS_DIR . '/wp-includes/Requests/Utility/CaseInsensitiveDictionary.php';
}

if (!class_exists('Requests_Exception')) {
    require WORDPRESS_DIR . '/wp-includes/Requests/Exception.php';
}
