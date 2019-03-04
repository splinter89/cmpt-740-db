<?php
declare(strict_types=1);

namespace App\DataSource\Accommodation;

use Atlas\Mapper\Record;

/**
 * @method AccommodationRow getRow()
 */
class AccommodationRecord extends Record
{
    use AccommodationFields;
}
