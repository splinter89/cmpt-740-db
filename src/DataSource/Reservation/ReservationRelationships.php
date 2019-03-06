<?php
declare(strict_types=1);

namespace App\DataSource\Reservation;

use App\DataSource\Accommodation\Accommodation;
use App\DataSource\User\User;
use Atlas\Mapper\MapperRelationships;

class ReservationRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('accommodation', Accommodation::class, [
            'accommodation_id' => 'id',
        ]);
        $this->manyToOne('guest_user', User::class, [
            'guest_user_id' => 'id',
        ]);
    }
}
