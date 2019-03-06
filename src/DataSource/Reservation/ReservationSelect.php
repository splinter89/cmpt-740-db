<?php
declare(strict_types=1);

namespace App\DataSource\Reservation;

use Atlas\Mapper\MapperSelect;

/**
 * @method ReservationRecord|null fetchRecord()
 * @method ReservationRecord[] fetchRecords()
 * @method ReservationRecordSet fetchRecordSet()
 */
class ReservationSelect extends MapperSelect
{
}
