<?php
declare(strict_types=1);

namespace App\DataSource\Reservation;

use Atlas\Mapper\Record;

/**
 * @method ReservationRow getRow()
 */
class ReservationRecord extends Record
{
    use ReservationFields;
}
