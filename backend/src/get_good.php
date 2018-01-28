<?php

$good_id = $matches[1];

$query = mysqli_prepare($mysqli,
    'SELECT * FROM Goods WHERE id = ?'
);
mysqli_stmt_bind_param($query, 'i', $good_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

// TODO check errors

$data = mysqli_fetch_assoc($result);
if (!$data) {
    http_response_code(404);
    die();
}

echo(json_encode($data));
?>
