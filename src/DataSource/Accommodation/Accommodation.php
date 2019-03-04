<?php
declare(strict_types=1);

namespace App\DataSource\Accommodation;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method AccommodationTable getTable()
 * @method AccommodationRelationships getRelationships()
 * @method AccommodationRecord|null fetchRecord($primaryVal, array $with = [])
 * @method AccommodationRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method AccommodationRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method AccommodationRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method AccommodationRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method AccommodationRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method AccommodationSelect select(array $whereEquals = [])
 * @method AccommodationRecord newRecord(array $fields = [])
 * @method AccommodationRecord[] newRecords(array $fieldSets)
 * @method AccommodationRecordSet newRecordSet(array $records = [])
 * @method AccommodationRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method AccommodationRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Accommodation extends Mapper
{
}
