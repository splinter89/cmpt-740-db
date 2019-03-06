<?php
declare(strict_types=1);

namespace App\DataSource\User;

use Atlas\Mapper\MapperSelect;

/**
 * @method UserRecord|null fetchRecord()
 * @method UserRecord[] fetchRecords()
 * @method UserRecordSet fetchRecordSet()
 */
class UserSelect extends MapperSelect
{
}
