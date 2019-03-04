<?php
declare(strict_types=1);

namespace App\DataSource\Accommodation;

use App\DataSource\User\User;
use Atlas\Mapper\MapperRelationships;

class AccommodationRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('host_user', User::class, [
            'host_user_id' => 'id',
        ]);
    }
}
