<?php
/**
 * router.php
 *
 * Used ONLY by PHP's built-in server for local development.
 * This mimics common Nginx rewrite behavior.
 *
 * Run with: php -S localhost:80 -t public router.php
 */

// Parse the URL path (ignore query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Base public directory
$basePath = __DIR__ . '/public';

// Full filesystem path for the requested URI
$path = $basePath . $uri;

/**
 * 1. Serve existing files directly
 *
 * This allows CSS, JS, images, fonts, etc. to be handled
 * by the built-in server without PHP overhead.
 */
if ($uri !== '/' && is_file($path) && pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
    return false;
}

/**
 * 2. Serve directory index.php
 *
 * Example:
 *   /page/user-a/  ->  /page/user-a/index.php
 */
if (is_dir($path) && is_file($path . '/index.php')) {
    require $path . '/index.php';
    exit;
}

/**
 * 3. Drop `.php` extension
 *
 * Example:
 *   /page/user-a/profile  ->  /page/user-a/profile.php
 */
if (is_file($path . '.php')) {
    require $path . '.php';
    exit;
}

/**
 * 4. Nothing matched  ->  404
 *
 * This behaves more like production servers.
 * No silent fallback to index.php.
 */
require $basePath . '/404.php';
exit;
