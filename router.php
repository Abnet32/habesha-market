<?php
// PHP built-in server router
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove query string
$file = __DIR__ . $path;

// Serve static files directly
if (is_file($file) && !str_ends_with($path, '.php')) {
    return false; // Let PHP serve static files natively
}

// Default to index.php if directory
if (is_dir($file)) {
    $index = rtrim($file, '/') . '/index.php';
    if (is_file($index)) {
        $_SERVER['SCRIPT_FILENAME'] = $index;
        include $index;
        return true;
    }
}

// Serve PHP files
if (is_file($file) && str_ends_with($path, '.php')) {
    return false;
}

// 404 fallback
return false;
