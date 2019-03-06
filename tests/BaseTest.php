<?php

declare(strict_types=1);

namespace App\Test;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class BaseTest extends TestCase
{
    /** @var ContainerInterface */
    protected static $container;

    public static function setUpBeforeClass(): void
    {
        self::$container = require __DIR__.'/../config/container.php';
    }

    public function assertNoExceptions()
    {
        $this->assertTrue(true);
    }
}
