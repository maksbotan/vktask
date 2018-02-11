<?php

/*
 * This API endpoint lets user create a new Good.
 * POST body in JSON:
 * {
 *   "name": "...",
 *   "price": "...",
 *   "description": "...",
 *   "pic_url": "..."
 * }
 */

// true is `bool $assoc` param to get JSON object as PHP assoc arrays
$data = json_decode(file_get_contents('php://input'), true);
if ($data === NULL) {
    http_response_code(400); // Bad request
    die();
}

// This is the only API method that parses and validates somewhat complex JSON request
// So there is no point in generalizing it into some kind of JSON validator
if (!array_key_exists('name', $data) || !array_key_exists('price', $data)) {
    http_response_code(400);
    die();
}

if (!is_string($data['name']) || !is_int($data['price'])) {
    http_response_code(400);
    die();
}

$description = NULL;
$pic_url = NULL;
if (array_key_exists('description', $data)) {
    if (!is_string($data['description'])) {
        http_response_code(400);
        die();
    }

    $description = $data['description'];
}
if (array_key_exists('pic_url', $data)) {
    if (!is_string($data['pic_url'])) {
        http_response_code(400);
        die();
    }

    $pic_url = $data['pic_url'];
}

include '_cache_utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = mysqli_prepare($mysqli,
        'INSERT INTO Goods (name, price, description, pic_url) VALUES (?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param($query, 'siss', $data['name'], $data['price'], $description, $pic_url);
    if (!mysqli_stmt_execute($query)) {
        http_response_code(500);
        die();
    }

    $id = mysqli_insert_id($mysqli);

    invalidate_cache($memcache, $id, $data['price']);

    echo(json_encode(["ok" => true, "id" => $id]));
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = $matches[1];

    $query = mysqli_prepare($mysqli,
        'UPDATE Goods SET name = ?, price = ?, description = ?, pic_url = ? WHERE id = ?'
    );
    mysqli_stmt_bind_param($query, 'sissi',
        $data['name'], $data['price'], $description, $pic_url,
        $id
    );
    if (!mysqli_stmt_execute($query)) {
        http_response_code(500);
        die();
    }

    invalidate_cache($memcache, $id, $data['price']);

    echo(json_encode(["ok" => true, "updated" => mysqli_affected_rows($mysqli) > 0]));
}
?>
