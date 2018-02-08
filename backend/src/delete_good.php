<?php

/*
 * This view lets user delete a good. Simple as that.
 */

$good_id = $matches[1];

$query = mysqli_prepare($mysqli,
    'DELETE FROM Goods WHERE id = ?'
);
mysqli_stmt_bind_param($query, 'i', $good_id);
if (!mysqli_stmt_execute($query)) {
    http_response_code(500);
    die();
}

$rows = mysqli_affected_rows($mysqli);

echo(json_encode([
    "ok" => true,
    "deleted" => $rows > 0
]));

?>
