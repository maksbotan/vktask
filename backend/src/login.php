<?php

/*
 * This view accepts username and password in JSON and lets user log in, storing token in Memcached
 */

$data = json_decode(file_get_contents('php://input'), true);
if ($data === NULL) {
    http_response_code(400); // Bad request
    die();
}

if (!array_key_exists('username', $data) || !array_key_exists('password', $data) ||
    !is_string($data['username']) || !is_string($data['password'])) {
    http_response_code(400);
    die();
}

$query = mysqli_prepare($mysqli,
    'SELECT password_hash FROM Users WHERE username = ?'
);
mysqli_stmt_bind_param($query, 's', $data['username']);
if (!mysqli_stmt_execute($query)) {
    http_response_code(500);
    die();
}

if (!($result = mysqli_stmt_get_result($query))) {
    http_response_code(500);
    die();
}

if (!($db_data = mysqli_fetch_assoc($result))) {
    http_response_code(404);
    die();
}

if (!password_verify($data['password'], $db_data['password_hash'])) {
    http_response_code(403);
    die();
}

$token = uuid_create();

// Store token in memcached for 10 days
memcache_set($memcache, "token:$token", $data['username'], 0, 10*24*60*60);

echo(json_encode(['ok' => true, 'token' => $token]));
?>
