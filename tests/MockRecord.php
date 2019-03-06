<?php

declare(strict_types=1);

namespace App\Test;

use Atlas\Mapper\Record;

class MockRecord extends Record
{
    protected $fields = [];

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function __get(string $field)
    {
        return $this->fields[$field];
    }

    public function __set(string $field, $value): void
    {
        $this->fields[$field] = $value;
    }

    public function __isset(string $field): bool
    {
        return isset($this->fields[$field]);
    }

    public function __unset($field): void
    {
        unset($this->fields[$field]);
    }
}
