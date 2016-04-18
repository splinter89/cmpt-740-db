<?php

define('INSERT_USER_QUERY', "INSERT INTO `user` (`name`, `date_created`) VALUES (:name, :date_created)");
define('INSERT_ACCOMMODATION_QUERY', "INSERT INTO `accommodation` (
      `host_user_id`,
      `city`,
      `address`,
      `price`,
      `type`,
      `has_washer`,
      `has_wifi`,
      `has_tv`,
      `date_created`
    )
    VALUES
      (
        :host_user_id,
        :city,
        :address,
        :price,
        :type,
        :has_washer,
        :has_wifi,
        :has_tv,
        :date_created
      )");
define('INSERT_RESERVATION_QUERY', "INSERT INTO `reservation` (
      `accommodation_id`,
      `guest_user_id`,
      `date_from`,
      `date_to`,
      `date_created`
    )
    VALUES
      (
        :accommodation_id,
        :guest_user_id,
        :date_from,
        :date_to,
        :date_created
      )");
