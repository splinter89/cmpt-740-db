<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ReservationRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment as Twig;

class ReservationController extends BaseController
{
    protected $reservationRepository;

    public function __construct(Response $response, Twig $twig, ReservationRepository $reservationRepository)
    {
        parent::__construct($response, $twig);
        $this->reservationRepository = $reservationRepository;
    }

    public function index(): Response
    {
        return $this->render('reservations/index.html.twig');
    }

    public function create(Request $request): Response
    {
        $this->reservationRepository->logQueries();
        $post = $request->getParsedBody();

        $keys = [
            'accommodation_id',
            'guest_user_id',
            'date_from',
            'date_to',
        ];
        $fields = array_intersect_key($post['reservation'] ?: [], array_flip($keys));
        $fields['date_from'] = date_create($fields['date_from'])->format('Y-m-d');
        $fields['date_to'] = date_create($fields['date_to'])->format('Y-m-d');
        $fields['date_to'] = max($fields['date_from'], $fields['date_to']);
        $fields['date_created'] = date('Y-m-d H:i:s');

        $new_reservation_id = '';
        $db_error = '';
        try {
            $new_reservation = $this->reservationRepository->create($fields);
            $new_reservation_id = $new_reservation->id;
        } catch (\Throwable $t) {
            $db_error = $t->getMessage();
        }

        return $this->render('reservations/index.html.twig', [
            'used_connections' => $this->reservationRepository->getUsedConnections(),
            'db_error' => $db_error,
            'reservation' => $fields,
            'new_reservation_id' => $new_reservation_id,
        ]);
    }
}
