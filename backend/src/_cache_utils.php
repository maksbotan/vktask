<?php

// Number of top pages of sorted lists to cache
$CACHE_DEPTH = 10;
// Time of life of cache entries, 2 hours
$CACHE_TIMEOUT = 2*60*60;

/* This function tries to load a page from cache.
 * $mode must be 'byprice' or 'byid', $offset is a multiple of 20
 */
function get_cache_page($memcache, $mode, $offset) {
    $key = "listcache:$mode:$offset";
    $data = memcache_get($memcache, $key);

    return $data;
}

/* This function stores a new page in cache
 */
function cache_page($memcache, $page_data, $mode, $offset) {
    global $CACHE_DEPTH;
    global $CACHE_TIMEOUT;

    if ($offset > $CACHE_DEPTH * 20) {
        return;
    }

    $key = "listcache:$mode:$offset";
    $value_key = "listcache:$mode:max_value";

    memcache_set($memcache, $key, $page_data, 0, $CACHE_TIMEOUT);

    $field = ($mode === 'byid') ? 'id' : 'price';
    // Store the max value of all cache to decide whether to invalidate later
    $max_value = $page_data[count($page_data) - 1][$field];
    $current_max = memcache_get($memcache, $value_key);
    if (!$current_max || $max_value > $current_max || $offset === $CACHE_DEPTH * 20) {
        memcache_set($memcache, $value_key, $max_value, 0, $CACHE_TIMEOUT);
    }
}

/* This function flushes the cache if it is affected by changes in given id or price value
 */
function invalidate_cache($memcache, $new_id, $new_price) {
    global $CACHE_DEPTH;

    $byid_key = "listcache:byid:max_value";
    $byprice_key = "listcache:byprice:max_value";

    $max_values = memcache_get($memcache, [$byid_key, $byprice_key]);

    if (array_key_exists($byid_key, $max_values)) {
        $max_id = $max_values[$byid_key];
        if ($max_id >= $new_id) {
            // if $new_id fits into cached sorted top of the list, we invalidate the entire cache
            for ($i = 0; $i < $CACHE_DEPTH; $i++) {
                $offset = $i * 20;
                memcache_delete($memcache, "listcache:byid:$offset");
            }
        }
    }

    if (array_key_exists($byprice_key, $max_values)) {
        $max_price = $max_values[$byprice_key];
        if ($max_price >= $new_price) {
            // likewise if $new_price lies in the sorted cached set
            for ($i = 0; $i < $CACHE_DEPTH; $i++) {
                $offset = $i * 20;
                memcache_delete($memcache, "listcache:byprice:$offset");
            }
        }
    }
}

?>
