<?php

$good_id = $matches[1];

$query = mysqli_prepare($mysqli,
    'SELECT * FROM Goods WHERE id = ?'
);
mysqli_stmt_bind_param($query, 'i', $good_id);
if (!mysqli_stmt_execute($query)) {
    http_response_code(500);
    die();
}

if (!($result = mysqli_stmt_get_result($query))) {
    http_response_code(500);
    die();
}

if (!($data = mysqli_fetch_assoc($result))) {
    http_response_code(404);
    die();
}

echo(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
?>
