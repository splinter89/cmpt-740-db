<?php
declare(strict_types=1);

namespace App\DataSource\User;

use Atlas\Mapper\Mapper;
use Atlas\Mapper\MapperEvents;
use Atlas\Mapper\Record;
use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Update;
use PDOStatement;

class UserEvents extends MapperEvents
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
     * @param UserRecord|Record $record
     */
    protected function assertValid($record): void
    {
        if (empty($record->name)) throw new \UnexpectedValueException('User name is empty.');
    }
}
