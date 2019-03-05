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

        $existing_reservations = $this->reservationRepository
            ->select(['accommodation_id' => $fields['accommodation_id']])
            ->fetchRecords();
        $got_conflict_reservations = false;
        foreach ($existing_reservations as $one) {
            if (!(($fields['date_to'] < $one->date_from) || ($one->date_to < $fields['date_from']))) {
                $got_conflict_reservations = true;
                break;
            }
        }

        $new_reservation_id = '';
        $db_error = '';
        if ($got_conflict_reservations) {
            $db_error = 'Got conflicting reservations';
        } else {
            $new_reservation = $this->reservationRepository->create($fields);
            $new_reservation_id = $new_reservation->id;
        }

        return $this->render('reservations/index.html.twig', [
            'used_connection_name' => 'master',
            'db_error' => $db_error,
            'reservation' => $fields,
            'new_reservation_id' => $new_reservation_id,
        ]);
    }
}
