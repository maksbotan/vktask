<?php

/*
 * This API endpoint returs a list of goods sorted either by id or by price (determined by URL)
 *
 * API supports basic pagination via two GET params:
 * - count (default = 20) -- number of goods to return
 * - offset (default = 0) -- number of goods to skip from the beginning of sorted list
 *
 * This is obviously not the best method of pagination, but the easiest to implement. Compared to number of
 * 'get_goods' calls, new goods are created or deleted rarely, so it will not disrupt the pagination too bad.
 */

// $mode is either 'byid' or 'byprice', guaranteed by the router
$mode = $matches[1];

$count = 20;
$offset = 0;

if (array_key_exists('count', $_GET)) {
    if (!is_numeric($_GET['count'])) {
        http_response_code(400); // Bad Request
        die();
    }
    $count = $_GET['count'] + 0;
}
if (array_key_exists('offset', $_GET)) {
    if (!is_numeric($_GET['offset'])) {
        http_response_code(400); // Bad Request
        die();
    }
    $offset = $_GET['offset'] + 0;
}

include '_cache_utils.php';

if ($count === 20) {
    // We cache only standard pages of 20 items
    $page = get_cache_page($memcache, $mode, $offset);
    if ($page) {
        echo(json_encode($page, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return;
    }
}

$inner_sql = 'SELECT id FROM Goods';

if ($mode === 'byprice') {
    $inner_sql .= ' ORDER BY price ASC';
} else {
    $inner_sql .= ' ORDER BY id ASC';
}

$inner_sql .= ' LIMIT ? OFFSET ?';

// A trick from https://habrahabr.ru/post/217521/ forces MySql to use index (either on id or price) to get
// a restricted set of ids and then read only rows from this set
$sql = 'SELECT * FROM Goods JOIN (' . $inner_sql . ') AS sub ON sub.id = Goods.id';

$query = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($query, 'ii', $count, $offset);
if (!mysqli_stmt_execute($query)) {
    http_response_code(500);
    die();
}

if (!($result = mysqli_stmt_get_result($query))) {
    http_response_code(500);
    die();
}

$goods = [];
while ($data = mysqli_fetch_assoc($result)) {
    $goods[] = $data;
}

if ($count === 20) {
    cache_page($memcache, $goods, $mode, $offset);
}

echo(json_encode($goods, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
?>
