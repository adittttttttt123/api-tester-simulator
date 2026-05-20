<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/' || $path === '/index' || $path === '/index.php') {
    require __DIR__ . '/../index.php';
} elseif ($path === '/history' || $path === '/history.php') {
    require __DIR__ . '/../history.php';
} else {
    http_response_code(404);
    echo "404 Not Found";
}
