<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataSource\Reservation\Reservation;
use App\DataSource\Reservation\ReservationRecord;
use App\DataSource\Reservation\ReservationSelect;

class ReservationRepository extends BaseRepository
{
    public function create(array $fields): ReservationRecord
    {
        $record = $this->atlas->newRecord(Reservation::class, $fields);
        $this->atlas->insert($record);
        return $record;
    }

    public function select(array $whereEquals = []): ReservationSelect
    {
        return $this->atlas->select(Reservation::class, $whereEquals);
    }
}
