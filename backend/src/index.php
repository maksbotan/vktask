<?php

/*
 * This is a single entry-point for the whole application, acting like a simple router.
 *
 * We rely on $_SERVER['PATH_INFO'] passed from nginx to contain URI like `/api/goods/5`.
 * This URI is then checked against pre-defined set of regexes to determine the handler.
 * The handler is just a PHP file that gets `include()`d into global scope.
 * If the file does not exist, we return HTTP 505.
 * Additionaly, request HTTP method is validated.
 */

$path = $_SERVER['PATH_INFO'];

// Route format: [method, regex, file_name]
$routes = [
    ['GET', '|^/api/good/(\d+)|', 'get_good']
];

$has_route = false;
foreach ($routes as $route) {
    if (preg_match($route[1], $path, $matches)) {
        $has_route = true;
        break;
    }
}

if (!$has_route) {
    http_response_code(404);
    die();
}

if ($_SERVER['REQUEST_METHOD'] !== $route[0]) {
    http_response_code(405); // Method Not Allowed
    die();
}

$src_file = $route[2] . '.php';
if (!file_exists($src_file)) {
    http_response_code(500); // Internal server error
    die();
}

$mysqli = mysqli_connect('mysql', 'vk', '6IK4l', 'vktask');

if (!$mysqli) {
    http_response_code(500);
    die();
}

$result = include $src_file;

if (!$result) {
    http_response_code(500);
    die();
}
?>
