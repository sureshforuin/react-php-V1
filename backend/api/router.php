<?php
/**
 * router.php — Only used by PHP built-in dev server (php -S).
 * Apache/Hostinger uses .htaccess instead, so this file is ignored in production.
 *
 * Usage:
 *   php -S localhost:8000 router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve real static files (images, css, etc.) directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Route everything through index.php
require __DIR__ . '/index.php';
