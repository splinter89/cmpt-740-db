<?php

require_once 'inc/common.php';

$used_connection_name = '';
$db_error = '';
if (!empty($_GET)) {
    $now = date('Y-m-d H:i:s');
    if (!empty($_GET['user'])) {
        $WriteConnection = DB::connection(WRITE_DB_CONNECTION_NAME);
        $used_connection_name = WRITE_DB_CONNECTION_NAME;

        $new_user = $_GET['user'];
        $new_user['date_created'] = $now;
        if (empty($new_user['name'])) {
            $db_error = 'User name is empty';
        } else {
            $new_user_id = $WriteConnection->insert(INSERT_USER_QUERY, $new_user);
        }
    } elseif (!empty($_GET['accommodation'])) {
        define('WRITE_DB_CONNECTION_NAME', chooseConnectionName(false, $_GET['accommodation']['city']));
        $WriteConnection = DB::connection(WRITE_DB_CONNECTION_NAME);
        $used_connection_name = WRITE_DB_CONNECTION_NAME;

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
        if (empty($new_accommodation['address'])) {
            $db_error = 'No address';
        } elseif (empty($new_accommodation['price'])) {
            $db_error = 'No price';
        } else {
            $new_accommodation_id = $WriteConnection->insert(INSERT_ACCOMMODATION_QUERY, $new_accommodation);
        }
    } elseif (!empty($_GET['reservation'])) {
        $WriteConnection = DB::connection(WRITE_DB_CONNECTION_NAME);
        $used_connection_name = WRITE_DB_CONNECTION_NAME;

        $new_reservation = $_GET['reservation'];
        $new_reservation['date_from'] = date_create($new_reservation['date_from'])->format('Y-m-d');
        $new_reservation['date_to'] = date_create($new_reservation['date_to'])->format('Y-m-d');
        $new_reservation['date_to'] = max($new_reservation['date_from'], $new_reservation['date_to']);
        $new_reservation['date_created'] = $now;

        $existing_reservations = $WriteConnection->select('SELECT date_from, date_to FROM reservation WHERE accommodation_id = :accommodation_id', [
            'accommodation_id' => $new_reservation['accommodation_id'],
        ]);
        $got_conflict_reservations = false;
        foreach ($existing_reservations as $one) {
            if (!(($new_reservation['date_to'] < $one['date_from']) || ($one['date_to'] < $new_reservation['date_from']))) {
                $got_conflict_reservations = true;
                break;
            }
        }

        if ($got_conflict_reservations) {
            $db_error = 'Got conflicting reservations';
        } else {
            $new_reservation_id = $WriteConnection->insert(INSERT_RESERVATION_QUERY, $new_reservation);
        }
    } elseif (!empty($_GET['search'])) {
        $ReadConnection = DB::connection(READ_DB_CONNECTION_NAME);
        $used_connection_name = READ_DB_CONNECTION_NAME;

        $search_params = $_GET['search'];
        $search_params['price_from'] = (float)$search_params['price_from'];
        $search_params['price_to'] = (float)$search_params['price_to'];

        $query = 'SELECT * FROM accommodation WHERE city = :city AND type = :type'
            .(!empty($search_params['price_from']) ? ' AND price >= :price_from' : '')
            .(!empty($search_params['price_to']) ? ' AND price <= :price_to' : '')
            .(!empty($search_params['has_washer']) ? ' AND has_washer = 1' : '')
            .(!empty($search_params['has_wifi']) ? ' AND has_wifi = 1' : '')
            .(!empty($search_params['has_tv']) ? ' AND has_tv = 1' : '')
            .' ORDER BY date_created DESC';
        if (empty($search_params['price_from'])) {
            unset($search_params['price_from']);
        }
        if (empty($search_params['price_to'])) {
            unset($search_params['price_to']);
        }
        unset($search_params['has_washer']);
        unset($search_params['has_wifi']);
        unset($search_params['has_tv']);
        $search_results = $ReadConnection->select($query, $search_params);
    }
}

$cities = readArrayFromFile('samples/cities.txt');
if (isset($ReadConnection) && $ReadConnection instanceof Database\Connection && $ReadConnection->getLastError()) {
    $db_error = $ReadConnection->getLastError();
} elseif (isset($WriteConnection) && $WriteConnection instanceof Database\Connection && $WriteConnection->getLastError()) {
    $db_error = $WriteConnection->getLastError();
}

?><!DOCTYPE html>
<html>
<head>
    <title>Replication Project</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<div class="connection_name notify">DB: <?=$used_connection_name ?></div>
<?php if (!empty($db_error)): ?>
    <div class="connection_error notify notify-red">ERROR: <?=$db_error ?></div>
<?php endif; ?>

<div style="text-align:center; width:600px; margin:50px auto;">
    <div class="block">
        <div class="title">Add User</div>

        <form action="" method="get">
            <div>Name: <input type="text" name="user[name]" value="<?=$_GET['user']['name'] ?>"></div>
            <button type="submit">add new user</button>
        </form>

        <?php if (!empty($new_user_id)): ?>
            <div>new record id: <?=$new_user_id ?></div>
        <?php endif; ?>
    </div>

    <div class="block">
        <div class="title">Add Accommodation</div>

        <form action="" method="get">
            <div>Host user id: <input type="text" name="accommodation[host_user_id]" value="<?=$_GET['accommodation']['host_user_id'] ?>"></div>
            <div>City:
                <select name="accommodation[city]">
                    <?php foreach ($cities as $city): ?>
                        <option value="<?=$city ?>" <?=($city == $_GET['accommodation']['city'])?'selected="selected"':'' ?>><?=$city ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>Address: <input type="text" name="accommodation[address]" value="<?=$_GET['accommodation']['address'] ?>"></div>
            <div>Price: <input type="text" name="accommodation[price]" value="<?=$_GET['accommodation']['price'] ?>"></div>
            <div>Type:
                <select name="accommodation[type]">
                    <?php foreach (['entire_home', 'private_room', 'shared_room'] as $type): ?>
                        <option value="<?=$type ?>" <?=($type == $_GET['accommodation']['type'])?'selected="selected"':'' ?>><?=$type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><input type="checkbox" name="accommodation[has_washer]" value="1" <?=($_GET['accommodation']['has_washer'])?'checked="checked"':'' ?>>washer</label>
                <label><input type="checkbox" name="accommodation[has_wifi]" value="1" <?=($_GET['accommodation']['has_wifi'])?'checked="checked"':'' ?>>wifi</label>
                <label><input type="checkbox" name="accommodation[has_tv]" value="1" <?=($_GET['accommodation']['has_tv'])?'checked="checked"':'' ?>>tv</label>
            </div>
            <button type="submit">add new accommodation</button>
        </form>

        <?php if (!empty($new_accommodation_id)): ?>
            <div>new record id: <?=$new_accommodation_id ?></div>
        <?php endif; ?>
    </div>

    <div class="block">
        <div class="title">Add Reservation</div>

        <form action="" method="get">
            <div>Accommodation id: <input type="text" name="reservation[accommodation_id]" value="<?=$_GET['reservation']['accommodation_id'] ?>"></div>
            <div>Guest user id: <input type="text" name="reservation[guest_user_id]" value="<?=$_GET['reservation']['guest_user_id'] ?>"></div>
            <div>Dates:
                <input type="date" name="reservation[date_from]" value="<?=$_GET['reservation']['date_from'] ?>">
                &ndash;
                <input type="date" name="reservation[date_to]" value="<?=$_GET['reservation']['date_to'] ?>">
            </div>
            <button type="submit">add new reservation</button>
        </form>

        <?php if (!empty($new_reservation_id)): ?>
            <div>new record id: <?=$new_reservation_id ?></div>
        <?php endif; ?>
    </div>

    <div class="block">
        <div class="title">Find Accommodation</div>

        <form action="" method="get">
            <div>City:
                <select name="search[city]">
                    <?php foreach ($cities as $city): ?>
                        <option value="<?=$city ?>" <?=($city == $_GET['search']['city'])?'selected="selected"':'' ?>><?=$city ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>Price:
                <input type="text" name="search[price_from]" value="<?=$_GET['search']['price_from'] ?>" style="width:100px;">
                &ndash;
                <input type="text" name="search[price_to]" value="<?=$_GET['search']['price_to'] ?>" style="width:100px;">
            </div>
            <div>Type:
                <select name="search[type]">
                    <?php foreach (['entire_home', 'private_room', 'shared_room'] as $type): ?>
                        <option value="<?=$type ?>" <?=($type == $_GET['search']['type'])?'selected="selected"':'' ?>><?=$type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><input type="checkbox" name="search[has_washer]" value="1" <?=($_GET['search']['has_washer'])?'checked="checked"':'' ?>>washer</label>
                <label><input type="checkbox" name="search[has_wifi]" value="1" <?=($_GET['search']['has_washer'])?'checked="checked"':'' ?>>wifi</label>
                <label><input type="checkbox" name="search[has_tv]" value="1" <?=($_GET['search']['has_washer'])?'checked="checked"':'' ?>>tv</label>
            </div>
            <button type="submit">search</button>
        </form>

        <div style="margin-top:35px;">
            <?php if (!empty($search_results)): ?>
                <div class="title">Search Results: <?=count($search_results) ?></div>
                <table cellpadding="0" cellspacing="0" style="text-align:left;">
                    <?php foreach ($search_results as $row): ?>
                        <tr><td><pre><?=print_r($row, 1) ?></pre></td></tr>
                    <?php endforeach; ?>
                </table>
            <?php elseif (!empty($_GET['search'])): ?>
                <div style="text-align:left;">Sorry, no results</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
