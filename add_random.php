<?php

require_once 'inc/common.php';
$Connection = DB::connection();

$is_house = trueWithProbability(0.5);
$city = pickFromFile('samples/cities.txt');
$address = mt_rand(1000, 9999).($is_house ? '' : '-'.mt_rand(1, 299))
    .' '.pickFromFile('samples/streets.txt')
    .', V'.pickFrom([5, 6]).pickFrom(range('K', 'Z'))
    .' '.mt_rand(1, 9).pickFrom(range('A', 'Z')).mt_rand(1, 9);
$price = round(mt_rand(700, 1200) / 20) * 20;
$type = $is_house ? 'entire_home' : pickFrom(['private_room', 'shared_room']);
$has_washer = pickFrom([0, 1]);
$has_wifi = pickFrom([0, 1]);
$has_tv = pickFrom([0, 1]);

$date_from = date_create()->add(new DateInterval('P'.mt_rand(1, 60).'D'));
$date_to = clone $date_from;
$date_to = $date_to->add(new DateInterval('P'.mt_rand(2, 120).'D'));

$now = date('Y-m-d H:i:s');

$host_user_id = handleRandomUserName(pickFromFile('samples/names.txt'), $Connection);
$guest_user_id = handleRandomUserName(pickFromFile('samples/names.txt'), $Connection);

$accommodation = [
    'host_user_id' => $host_user_id,
    'city' => $city,
    'address' => $address,
    'price' => $price,
    'type' => $type,
    'has_washer' => $has_washer,
    'has_wifi' => $has_wifi,
    'has_tv' => $has_tv,
    'date_created' => $now,
];
$accommodation_id = $Connection->insert(INSERT_ACCOMMODATION_QUERY, $accommodation);

$reservation = [
    'accommodation_id' => $accommodation_id,
    'guest_user_id' => $guest_user_id,
    'date_from' => $date_from->format('Y-m-d'),
    'date_to' => $date_to->format('Y-m-d'),
    'date_created' => $now,
];
$reservation_id = $Connection->insert(INSERT_RESERVATION_QUERY, $reservation);

echo json_encode([
    'host_user_id' => $host_user_id,
    'guest_user_id' => $guest_user_id,
    'accommodation_id' => $accommodation_id,
    'reservation_id' => $reservation_id,
]);
