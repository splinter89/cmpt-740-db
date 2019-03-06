<?php

declare(strict_types=1);

namespace App\Repository;

use Atlas\Mapper\Record;
use Atlas\Orm\Atlas;

abstract class BaseRepository
{
    protected $atlas;

    public function __construct(Atlas $atlas)
    {
        $this->atlas = $atlas;
    }

    public function update(Record $record): void
    {
        $this->atlas->update($record);
    }

    public function logQueries(bool $logQueries = true): void
    {
        $this->atlas->logQueries($logQueries);
    }

    public function getUsedConnections(): array
    {
        $queries = $this->atlas->getQueries();
        $names = [];
        foreach ($queries as $query) {
            $names[] = $query['connection'];
        }
        return array_unique($names);
    }
}
