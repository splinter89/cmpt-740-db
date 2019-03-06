<?php

declare(strict_types=1);

namespace App\Test;

class AppTest extends BaseTest
{
    public function testAppCreation()
    {
        $app = new \App\App(self::$container, require __DIR__.'/../config/router.php');
        $this->assertInstanceOf(\App\App::class, $app);
    }
}
