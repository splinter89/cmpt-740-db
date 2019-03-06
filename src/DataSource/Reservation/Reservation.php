<?php
declare(strict_types=1);

namespace App\DataSource\Reservation;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method ReservationTable getTable()
 * @method ReservationRelationships getRelationships()
 * @method ReservationRecord|null fetchRecord($primaryVal, array $with = [])
 * @method ReservationRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method ReservationRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method ReservationRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method ReservationRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method ReservationRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method ReservationSelect select(array $whereEquals = [])
 * @method ReservationRecord newRecord(array $fields = [])
 * @method ReservationRecord[] newRecords(array $fieldSets)
 * @method ReservationRecordSet newRecordSet(array $records = [])
 * @method ReservationRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method ReservationRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Reservation extends Mapper
{
}
