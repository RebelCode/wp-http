<?php

const VENDOR_DIR = __DIR__ . '/../vendor';
const WORDPRESS_DIR = VENDOR_DIR . '/johnpbloch/wordpress-core';

require_once VENDOR_DIR . '/autoload.php';

WP_Mock::bootstrap();
