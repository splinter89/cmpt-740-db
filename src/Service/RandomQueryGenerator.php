<?php

declare(strict_types=1);

namespace App\Service;

use App\DataSource\User\UserRecord;
use App\Repository\AccommodationRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Samples;

class RandomQueryGenerator
{
    private $userRepository;
    private $accommodationRepository;
    private $reservationRepository;

    public function __construct(
        UserRepository $userRepository,
        AccommodationRepository $accommodationRepository,
        ReservationRepository $reservationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->accommodationRepository = $accommodationRepository;
        $this->reservationRepository = $reservationRepository;
    }

    public function insert(): array
    {
        $is_house = $this->trueWithProbability(0.5);
        $city = $this->pickFrom(Samples::CITIES);
        $address = mt_rand(1000, 9999).($is_house ? '' : '-'.mt_rand(1, 299))
            .' '.$this->pickFrom(Samples::STREETS)
            .', V'.$this->pickFrom([5, 6]).$this->pickFrom(range('K', 'Z'))
            .' '.mt_rand(1, 9).$this->pickFrom(range('A', 'Z')).mt_rand(1, 9);
        $price = round(mt_rand(700, 1200) / 20) * 20;
        $type = $is_house ? 'entire_home' : $this->pickFrom(['private_room', 'shared_room']);
        $has_washer = $this->pickFrom([0, 1]);
        $has_wifi = $this->pickFrom([0, 1]);
        $has_tv = $this->pickFrom([0, 1]);
        $date_created = date('Y-m-d H:i:s');

        $host_user = $this->handleRandomUserName($this->pickFrom(Samples::NAMES));
        $guest_user = $this->handleRandomUserName($this->pickFrom(Samples::NAMES));
        $host_user_id = $host_user->id;
        $guest_user_id = $guest_user->id;

        $date_from = date_create()->add(new \DateInterval('P'.mt_rand(1, 60).'D'));
        $date_to = clone $date_from;
        $date_to = $date_to->add(new \DateInterval('P'.mt_rand(2, 120).'D'));

        $date_from = $date_from->format('Y-m-d');
        $date_to = $date_to->format('Y-m-d');

        $accommodation = $this->accommodationRepository->create(compact(
            'host_user_id',
            'city',
            'address',
            'price',
            'type',
            'has_washer',
            'has_wifi',
            'has_tv',
            'date_created'
        ));
        $accommodation_id = $accommodation->id;

        $reservation = $this->reservationRepository->create(compact(
            'accommodation_id',
            'guest_user_id',
            'date_from',
            'date_to',
            'date_created'
        ));
        $reservation_id = $reservation->id;

        return compact('host_user_id', 'guest_user_id', 'accommodation_id', 'reservation_id');
    }

    public function select(): array
    {
        $city = $this->pickFrom(Samples::CITIES);
        $type = $this->pickFrom(['entire_home', 'private_room', 'shared_room']);
        $has_washer = $this->trueWithProbability(0.4);
        $has_wifi = $this->trueWithProbability(0.9);
        $has_tv = $this->trueWithProbability(0.4);
        $price_from = mt_rand(700, 900);
        $price_to = $price_from + mt_rand(10, 300);

        $search_params = compact('city', 'type', 'has_washer', 'has_wifi', 'has_tv');
        $results = $this->accommodationRepository->select()
            ->whereEquals($search_params)
            ->where('price >= ', $price_from)
            ->where('price <= ', $price_to)
            ->orderBy('date_created DESC')
            ->fetchRecordSet();

        $search_params['price_from'] = $price_from;
        $search_params['price_to'] = $price_to;
        return compact('search_params', 'results');
    }

    public function update(): array
    {
        $count = $this->accommodationRepository->select()->fetchCount();
        $offset = mt_rand(0, $count - 1);
        $accommodation = $this->accommodationRepository->select()
            ->limit(1)
            ->offset($offset)
            ->fetchRecord();

        $old_price = $accommodation->price;
        $accommodation->price = round($accommodation->price * mt_rand(6, 17) / (10 * 20)) * 20;
        $this->accommodationRepository->update($accommodation);

        return [
            'accommodation_id' => $accommodation->id,
            'old_price' => $old_price,
            'new_price' => (string)$accommodation->price,
        ];
    }

    protected function trueWithProbability($probability): bool
    {
        $probability = max(min($probability, 1), 0);
        return (mt_rand() / mt_getrandmax() <= $probability);
    }

    protected function pickFrom(array $array)
    {
        return (!empty($array)) ? $array[array_rand($array)] : null;
    }

    protected function handleRandomUserName($user_name): UserRecord
    {
        $user = null;
        $use_existing = $this->trueWithProbability(0.6);
        if ($use_existing) {
            $hosts = $this->userRepository->select()
                ->where('name = ', $user_name)
                ->fetchRecords();
            if (!empty($hosts)) {
                $user = $this->pickFrom($hosts);
            }
        }
        if (empty($user)) {
            $user = $this->userRepository->create([
                'name' => $user_name,
                'date_created' => date('Y-m-d H:i:s'),
            ]);
        }
        if (empty($user)) {
            throw new \RuntimeException('Failed to create user');
        }
        return $user;
    }
}
