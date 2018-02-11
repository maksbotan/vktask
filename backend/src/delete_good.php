<?php

/*
 * This view lets user delete a good. Simple as that.
 */

$good_id = $matches[1];

include '_cache_utils.php';

$query = mysqli_prepare($mysqli,
    'DELETE FROM Goods WHERE id = ?'
);
mysqli_stmt_bind_param($query, 'i', $good_id);
if (!mysqli_stmt_execute($query)) {
    http_response_code(500);
    die();
}

$rows = mysqli_affected_rows($mysqli);
if ($rows > 0) {
    // Let's just flush whole by price cache unconditionally instead of fetching the price from MySQL
    invalidate_cache($memcache, $good_id, 0);
}

echo(json_encode([
    "ok" => true,
    "deleted" => $rows > 0
]));

?>
