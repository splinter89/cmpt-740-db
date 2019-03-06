<?php
declare(strict_types=1);

namespace App\DataSource\Accommodation;

use Atlas\Mapper\Mapper;
use Atlas\Mapper\MapperEvents;
use Atlas\Mapper\Record;
use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Update;
use PDOStatement;

class AccommodationEvents extends MapperEvents
{
    public function beforeInsert(Mapper $mapper, Record $record): void
    {
        $this->assertValid($record);
    }

    public function beforeUpdate(Mapper $mapper, Record $record): void
    {
        $this->assertValid($record);
    }

    /**
     * @param AccommodationRecord|Record $record
     */
    protected function assertValid($record): void
    {
        if (empty($record->address)) throw new \UnexpectedValueException('Address is empty.');
        if (empty($record->price)) throw new \UnexpectedValueException('Price is empty.');
    }
}
