<?php
declare(strict_types=1);

namespace App\DataSource\Accommodation;

use Atlas\Mapper\RecordSet;

/**
 * @method AccommodationRecord offsetGet($offset)
 * @method AccommodationRecord appendNew(array $fields = [])
 * @method AccommodationRecord|null getOneBy(array $whereEquals)
 * @method AccommodationRecordSet getAllBy(array $whereEquals)
 * @method AccommodationRecord|null detachOneBy(array $whereEquals)
 * @method AccommodationRecordSet detachAllBy(array $whereEquals)
 * @method AccommodationRecordSet detachAll()
 * @method AccommodationRecordSet detachDeleted()
 */
class AccommodationRecordSet extends RecordSet
{
}
