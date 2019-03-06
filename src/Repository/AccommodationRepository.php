<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataSource\Accommodation\Accommodation;
use App\DataSource\Accommodation\AccommodationRecord;
use App\DataSource\Accommodation\AccommodationSelect;

class AccommodationRepository extends BaseRepository
{
    public function create(array $fields): AccommodationRecord
    {
        $record = $this->atlas->newRecord(Accommodation::class, $fields);
        $this->atlas->insert($record);
        return $record;
    }

    public function select(array $whereEquals = []): AccommodationSelect
    {
        return $this->atlas->select(Accommodation::class, $whereEquals);
    }
}
