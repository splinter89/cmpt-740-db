<?php
declare(strict_types=1);

namespace App\DataSource\Reservation;

use Atlas\Mapper\RecordSet;

/**
 * @method ReservationRecord offsetGet($offset)
 * @method ReservationRecord appendNew(array $fields = [])
 * @method ReservationRecord|null getOneBy(array $whereEquals)
 * @method ReservationRecordSet getAllBy(array $whereEquals)
 * @method ReservationRecord|null detachOneBy(array $whereEquals)
 * @method ReservationRecordSet detachAllBy(array $whereEquals)
 * @method ReservationRecordSet detachAll()
 * @method ReservationRecordSet detachDeleted()
 */
class ReservationRecordSet extends RecordSet
{
}
