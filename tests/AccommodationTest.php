<?php

declare(strict_types=1);

namespace App\Test;

use App\DataSource\Accommodation\AccommodationEvents;

class AccommodationTest extends BaseTest
{
    /**
     * @param array $fields
     * @dataProvider validDataProvider
     */
    public function testValidInsert(array $fields)
    {
        $events = new AccommodationEvents;
        $events->beforeInsert(new MockMapper, new MockRecord($fields));

        $this->assertNoExceptions();
    }

    public function validDataProvider()
    {
        return [
            [['id' => '', 'city' => 'Vancouver', 'address' => '1234 Name St.', 'price' => '100']],
            [['id' => '', 'city' => 'Burnaby', 'address' => '56-1234 Name Street', 'price' => '1']],
        ];
    }

    /**
     * @param array $fields
     * @param string $exception
     * @param string $message
     * @dataProvider invalidDataProvider
     */
    public function testInvalidUpdate(array $fields, string $exception, string $message)
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        $events = new AccommodationEvents;
        $events->beforeUpdate(new MockMapper, new MockRecord($fields));
    }

    public function invalidDataProvider()
    {
        return [
            [['id' => '', 'city' => 'Vancouver', 'address' => '', 'price' => '100'], \UnexpectedValueException::class, 'Address is empty.'],
            [['id' => '', 'city' => 'Vancouver', 'address' => '1234 Name St.', 'price' => '0'], \UnexpectedValueException::class, 'Price is empty.'],
        ];
    }
}
