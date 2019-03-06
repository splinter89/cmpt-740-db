<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataSource\User\User;
use App\DataSource\User\UserRecord;
use App\DataSource\User\UserSelect;

class UserRepository extends BaseRepository
{
    public function create(array $fields): UserRecord
    {
        $record = $this->atlas->newRecord(User::class, $fields);
        $this->atlas->insert($record);
        return $record;
    }

    public function select(array $whereEquals = []): UserSelect
    {
        return $this->atlas->select(User::class, $whereEquals);
    }
}
