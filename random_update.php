<?php

require_once 'inc/common.php';
$Connection = DB::connection();

$count = $Connection->select('SELECT COUNT(1) c FROM accommodation')[0]['c'];
$offset = mt_rand(1, $count - 1);
$row = $Connection->select('SELECT id, price FROM accommodation LIMIT '.$offset.', 1')[0];

$new_price = round($row['price'] * mt_rand(6, 17) / (10 * 20)) * 20;
$Connection->update('UPDATE accommodation SET price = :p WHERE id = :id', ['id' => $row['id'], 'p' => $new_price]);

echo json_encode([
    'accommodation_id' => $row['id'],
    'price' => $row['price'],
    'new_price' => (string)$new_price,
]);
