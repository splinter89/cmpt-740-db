<?php
declare(strict_types=1);

namespace App\DataSource\Reservation;

use Atlas\Mapper\Mapper;
use Atlas\Mapper\MapperEvents;
use Atlas\Mapper\Record;
use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Update;
use PDOStatement;

class ReservationEvents extends MapperEvents
{
    public function beforeInsert(Mapper $mapper, Record $record): void
    {
        $this->assertValid($mapper, $record);
    }

    public function beforeUpdate(Mapper $mapper, Record $record): void
    {
        $this->assertValid($mapper, $record);
    }

    /**
     * @param Reservation|Mapper $mapper
     * @param ReservationRecord|Record $record
     */
    protected function assertValid($mapper, $record): void
    {
        if ($record->date_from > $record->date_to) {
            throw new \UnexpectedValueException('"date_from" is greater than "date_to".');
        }

        $existing_reservations = $mapper
            ->select(['accommodation_id' => $record->accommodation_id])
            ->fetchRecords();
        $got_conflict = false;
        foreach ($existing_reservations as $one) {
            if (!(($record->date_to < $one->date_from) || ($one->date_to < $record->date_from))) {
                $got_conflict = true;
                break;
            }
        }
        if ($got_conflict) throw new \RuntimeException('Got conflicting reservations.');
    }
}
