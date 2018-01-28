<?php


/*
 * This script creates tables and indices in the DB
 * No migrations or any other fancy stuff is done for the test task
 */

$mysqli = mysqli_connect('mysql', 'vk', '6IK4l', 'vktask');

if (!$mysqli) {
    die(mysqli_connect_error());
}

$res = mysqli_query($mysqli,
    'CREATE TABLE Goods (' .
    '   id INT AUTO_INCREMENT PRIMARY KEY,' .
    '   name VARCHAR(255) NOT NULL,' .
    '   description TEXT NULL,' .
    '   price INT NOT NULL,' .
    '   pic_url VARCHAR(1024) NULL' .
    ')'
);

if (!$res) {
    die(mysqli_error($mysqli));
}

$res = mysqli_query($mysqli,
    'CREATE INDEX good_price ON Goods (' .
    '   price ASC' .
    ')'
);

if (!$res) {
    die(mysqli_error($mysqli));
}
?>
