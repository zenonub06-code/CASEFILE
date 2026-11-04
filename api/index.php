
<?php

$root = dirname(__DIR__);
chdir($root);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rawurldecode($path);

if ($path === '/' || str_ends_with($path, '/')) {
    $path .= 'index.php';
}

$relative = ltrim($path, '/');
$target = $root . '/' . $relative;

// Pastikan file berada di dalam folder project
$realTarget = realpath($target);
$realRoot = realpath($root);

if (
    $realTarget === false ||
    !str_starts_with($realTarget, $realRoot . DIRECTORY_SEPARATOR) &&
    $realTarget !== $realRoot
) {
    http_response_code(404);
    exit('File tidak ditemukan');
}

// Jalankan file PHP
if (strtolower(pathinfo($realTarget, PATHINFO_EXTENSION)) === 'php') {
    require $realTarget;
    exit;
}

// Tampilkan file statis seperti CSS, JS, dan gambar
$mimeTypes = [
    'css' => 'text/css',
    'js' => 'application/javascript',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'webp' => 'image/webp',
    'gif' => 'image/gif',
    'svg' => 'image/svg+xml',
    'ico' => 'image/x-icon',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
];

$extension = strtolower(pathinfo($realTarget, PATHINFO_EXTENSION));

header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
readfile($realTarget);
