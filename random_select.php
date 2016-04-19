<?php

require_once 'inc/common.php';
$Connection = DB::connection();

$price_from = mt_rand(700, 900);
$price_to = $price_from + mt_rand(10, 300);
$require_washer = trueWithProbability(0.4);
$require_wifi = trueWithProbability(0.9);

$query = 'SELECT * FROM accommodation WHERE city = :city AND price >= :price_from AND price <= :price_to AND type = :type'
    .($require_washer ? ' AND has_washer = 1' : '')
    .($require_wifi ? ' AND has_wifi = 1' : '')
    .' ORDER BY date_created DESC';
$search_params = [
    'city' => pickFromFile('samples/cities.txt'),
    'price_from' => $price_from,
    'price_to' => $price_to,
    'type' => pickFrom(['entire_home', 'private_room', 'shared_room']),
];
$results = $Connection->select($query, $search_params);

if ($require_washer) {
    $search_params['has_washer'] = 1;
}
if ($require_wifi) {
    $search_params['has_wifi'] = 1;
}
echo json_encode([
    'search_params' => $search_params,
    'results' => $results,
]);
