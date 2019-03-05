<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\AccommodationRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment as Twig;

class AccommodationController extends BaseController
{
    protected $accommodationRepository;

    public function __construct(Response $response, Twig $twig, AccommodationRepository $accommodationRepository)
    {
        parent::__construct($response, $twig);
        $this->accommodationRepository = $accommodationRepository;
    }

    public function index(): Response
    {
        return $this->render('accommodations/index.html.twig', [
            'cities' => \App\Samples::CITIES,
            'types' => ['entire_home', 'private_room', 'shared_room'],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->accommodationRepository->logQueries();
        $post = $request->getParsedBody();

        $keys = [
            'host_user_id',
            'city',
            'address',
            'price',
            'type',
            'has_washer',
            'has_wifi',
            'has_tv',
        ];
        $fields = array_intersect_key($post['accommodation'] ?: [], array_flip($keys));
        $fields['price'] = (float)$fields['price'];
        $fields['date_created'] = date('Y-m-d H:i:s');

        $new_accommodation_id = '';
        $db_error = '';
        if (empty($fields['address'])) {
            $db_error = 'No address';
        } elseif (empty($fields['price'])) {
            $db_error = 'No price';
        } else {
            $new_accommodation = $this->accommodationRepository->create($fields);
            $new_accommodation_id = $new_accommodation->id;
        }

        return $this->render('accommodations/index.html.twig', [
            'used_connections' => $this->accommodationRepository->getUsedConnections(),
            'db_error' => $db_error,
            'cities' => \App\Samples::CITIES,
            'types' => ['entire_home', 'private_room', 'shared_room'],
            'accommodation' => $fields,
            'new_accommodation_id' => $new_accommodation_id,
        ]);
    }

    public function search(Request $request): Response
    {
        $this->accommodationRepository->logQueries();
        $params = $request->getQueryParams();

        $search_params = [];
        $search_results = [];
        if (!empty($params['search'])) {
            $keys = [
                'city',
                'price_from',
                'price_to',
                'type',
                'has_washer',
                'has_wifi',
                'has_tv',
            ];
            $search_params = array_intersect_key($params['search'] ?: [], array_flip($keys));
            $price_from = (float)$search_params['price_from'];
            $price_to = (float)$search_params['price_to'];

            $whereEquals = array_diff_key($search_params, array_flip(['price_from', 'price_to']));
            $select = $this->accommodationRepository->select($whereEquals);
            if (!empty($price_from)) {
                $select->where('price >= ', $price_from);
            }
            if (!empty($price_to)) {
                $select->where('price <= ', $price_to);
            }
            $search_results = $select->orderBy('date_created DESC')->fetchRecords();
        }

        return $this->render('accommodations/search.html.twig', [
            'used_connections' => $this->accommodationRepository->getUsedConnections(),
            'cities' => \App\Samples::CITIES,
            'types' => ['entire_home', 'private_room', 'shared_room'],
            'search' => $search_params,
            'search_results' => array_map(
                function ($record) { return str_replace('",', '",'.PHP_EOL, json_encode($record)); },
                $search_results
            ),
        ]);
    }
}
