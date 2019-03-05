<?php

declare(strict_types=1);

use App\Controller\AccommodationController;
use App\Controller\HomeController;
use App\Controller\RandomController;
use App\Controller\ReservationController;
use App\Controller\UserController;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $r) {
    $r->get('/', HomeController::class);

    $r->get('/users/', [UserController::class, 'index']);
    $r->post('/users/', [UserController::class, 'create']);
    $r->get('/accommodations/', [AccommodationController::class, 'index']);
    $r->post('/accommodations/', [AccommodationController::class, 'create']);
    $r->get('/accommodations/search/', [AccommodationController::class, 'search']);
    $r->get('/reservations/', [ReservationController::class, 'index']);
    $r->post('/reservations/', [ReservationController::class, 'create']);

    $r->get('/random/{type:(?:insert|select|update)}/', RandomController::class);
});
