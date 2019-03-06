<?php

declare(strict_types=1);

namespace App\Test;

use App\DataSource\User\UserEvents;

class UserTest extends BaseTest
{
    /**
     * @param array $fields
     * @dataProvider validDataProvider
     */
    public function testValidInsert(array $fields)
    {
        $events = new UserEvents;
        $events->beforeInsert(new MockMapper, new MockRecord($fields));

        $this->assertNoExceptions();
    }

    public function validDataProvider()
    {
        return [
            [['id' => '', 'name' => 'Name', 'date_created' => '']],
            [['id' => '', 'name' => 'Not empty', 'date_created' => '']],
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

        $events = new UserEvents;
        $events->beforeUpdate(new MockMapper, new MockRecord($fields));
    }

    public function invalidDataProvider()
    {
        return [
            [['id' => '', 'name' => '', 'date_created' => ''], \UnexpectedValueException::class, 'User name is empty.'],
        ];
    }
}
