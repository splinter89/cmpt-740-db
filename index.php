<?php

require_once 'inc/common.php';
$Connection = DB::connection();

if (!empty($_GET)) {
//    var_dump($_GET);if(1)die;
    $now = date('Y-m-d H:i:s');
    if (!empty($_GET['user'])) {
        $new_user = $_GET['user'];
        $new_user['date_created'] = $now;
        $new_user_id = $Connection->insert(INSERT_USER_QUERY, $new_user);
    } elseif (!empty($_GET['accommodation'])) {
        $new_accommodation = $_GET['accommodation'];
        $new_accommodation['price'] = (float)$new_accommodation['price'];
        if (empty($new_accommodation['has_washer'])) {
            $new_accommodation['has_washer'] = 0;
        }
        if (empty($new_accommodation['has_wifi'])) {
            $new_accommodation['has_wifi'] = 0;
        }
        if (empty($new_accommodation['has_tv'])) {
            $new_accommodation['has_tv'] = 0;
        }
        $new_accommodation['date_created'] = $now;
        $new_accommodation_id = $Connection->insert(INSERT_ACCOMMODATION_QUERY, $new_accommodation);
    } elseif ($_GET['reservation']) {
        $new_reservation = $_GET['reservation'];
        $new_reservation['date_from'] = date_create($new_reservation['date_from'])->format('Y-m-d');
        $new_reservation['date_to'] = date_create($new_reservation['date_to'])->format('Y-m-d');
        $new_reservation['date_to'] = max($new_reservation['date_from'], $new_reservation['date_to']);
        $new_reservation['date_created'] = $now;
        $new_reservation_id = $Connection->insert(INSERT_RESERVATION_QUERY, $new_reservation);
    }
}

$cities = readArrayFromFile('samples/cities.txt');

?><!DOCTYPE html>
<html>
<head>
    <title>Replication Test Project</title>
    <style type="text/css">
        .block {
            border: 1px solid grey;
            margin: 15px;
            padding: 5px;
        }

        .title {
            font-weight: bold;
            text-align: left;
        }

        input, button {
            margin: 5px 0;
        }

        label {
            margin-right: 15px;
        }

    </style>
</head>
<body>
<div style="text-align:center; width:600px; margin:200px auto 0;">
    <div class="block">
        <div class="title">Add User:</div>
        <?php if (!empty($new_user_id)): ?>
            <div>new record id: <?=$new_user_id ?></div>
        <?php endif; ?>
        <form action="" method="get">
            <div>Name: <input type="text" name="user[name]" value=""></div>
            <button type="submit">add new user</button>
        </form>
    </div>

    <div class="block">
        <div class="title">Add Accommodation:</div>
        <?php if (!empty($new_accommodation_id)): ?>
            <div>new record id: <?=$new_accommodation_id ?></div>
        <?php endif; ?>
        <form action="" method="get">
            <div>Host user id: <input type="text" name="accommodation[host_user_id]" value=""></div>
            <div>City:
                <select name="accommodation[city]">
                    <?php foreach ($cities as $city): ?>
                        <option value="<?=$city ?>"><?=$city ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>Address: <input type="text" name="accommodation[address]" value=""></div>
            <div>Price: <input type="text" name="accommodation[price]" value=""></div>
            <div>Type:
                <select name="accommodation[type]">
                    <?php foreach (['entire_home', 'private_room', 'shared_room'] as $type): ?>
                        <option value="<?=$type ?>"><?=$type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><input type="checkbox" name="accommodation[has_washer]" value="1">washer</label>
                <label><input type="checkbox" name="accommodation[has_wifi]" value="1">wifi</label>
                <label><input type="checkbox" name="accommodation[has_tv]" value="1">tv</label>
            </div>
            <button type="submit">add new accommodation</button>
        </form>
    </div>

    <div class="block">
        <div class="title">Add Reservation:</div>
        <?php if (!empty($new_reservation_id)): ?>
            <div>new record id: <?=$new_reservation_id ?></div>
        <?php endif; ?>
        <form action="" method="get">
            <div>Accommodation id: <input type="text" name="reservation[accommodation_id]" value=""></div>
            <div>Guest user id: <input type="text" name="reservation[guest_user_id]" value=""></div>
            <div>Dates:
                <input type="date" name="reservation[date_from]" value="">&ndash;
                <input type="date" name="reservation[date_to]" value="">
            </div>
            <button type="submit">add new reservation</button>
        </form>
    </div>

    <div class="block">
        Find Accommodation:
    </div>
</div>
</body>
</html>
