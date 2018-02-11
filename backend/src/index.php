<?php

/*
 * This is a single entry-point for the whole application, acting like a simple router.
 *
 * We rely on $_SERVER['PATH_INFO'] passed from nginx to contain URI like `/api/goods/5`.
 * This URI is then checked against pre-defined set of regexes to determine the handler.
 * The handler is just a PHP file that gets `include()`d into global scope.
 * If the file does not exist, we return HTTP 500.
 * Additionaly, request HTTP method is validated.
 */

$path = $_SERVER['PATH_INFO'];

// Route format: [method, regex, file_name]
$routes = [
    ['POST', '|^/api/login|', 'login'],
    ['GET', '|^/api/good/(\d+)|', 'get_good'],
    ['DELETE', '|^/api/good/(\d+)|', 'delete_good'],
    ['POST', '|^/api/good/new|', 'add_good'],
    ['PUT', '|^/api/good/(\d+)|', 'add_good'],
    ['GET', '@^/api/goods/(byid|byprice)@', 'get_goods'],
];

$seen_route = false;
$has_route = false;
foreach ($routes as $route) {
    if (preg_match($route[1], $path, $matches)) {
        $seen_route = true;
        if ($_SERVER['REQUEST_METHOD'] === $route[0]) {
            $has_route = true;
            break;
        }
    }
}

if (!$has_route) {
    if ($seen_route) {
        // This combination of flags means that URI matches some route, but not its method
        http_response_code(405); // Method Not Allowed
        die();
    } else {
        http_response_code(404);
        die();
    }
}


$src_file = $route[2] . '.php';
if (!file_exists($src_file)) {
    http_response_code(500); // Internal server error
    die();
}

// Have to use old 'memcache' extension, because PECL 'memcached' extenstion wants PHP 7...
$memcache = memcache_connect('memcached', 11211);
if (!$memcache) {
    http_response_code(500);
    die();
}

$username = null;
if ($route[2] !== 'login') {
    // We implement a very simple token-based auth
    // Client must provide HTTP 'Authorization:' header with value 'Token XXXXXX'
    // Token is then looked up in memcached. If it is missing, we return HTTP 401
    // Could have used JWT tokens to avoid lookup per each request, but should be good for demo anyway.

    if (!array_key_exists('HTTP_AUTHORIZATION', $_SERVER)) {
        header('WWW-Authenticate: Token');
        http_response_code(401); // Not authorized
        die();
    }
    $header = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
    if (count($header) !== 2 || $header[0] !== 'Token') {
        header('WWW-Authenticate: Token');
        http_response_code(401); // Not authorized
        die();
    }

    $username = memcache_get($memcache, "token:$header[1]");
    if (!$username) {
        // If token is not found in memcached, return error
        header('WWW-Authenticate: Token');
        http_response_code(401); // Not authorized
        die();
    }
}

$mysqli = mysqli_connect('mysql', 'vk', '6IK4l', 'vktask');

if (!$mysqli) {
    http_response_code(500);
    die();
}

header('Content-Type: application/json');

$result = include $src_file;

if (!$result) {
    http_response_code(500);
    die();
}
?>
