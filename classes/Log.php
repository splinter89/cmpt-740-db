<?php

use Docs\Log as LogDocs;

class Log
{
    use Singleton;
    use LogDocs;

    protected static $filename = '';

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param string $filename
     */
    public static function setUp($filename)
    {
        self::$filename = $filename;
    }

    /**
     * @return Logger
     */
    public static function logger()
    {
        $log = self::getInstance();
        if (is_null($log->logger)) {
            $log->logger = new Logger(self::$filename);
        }
        return $log->logger;
    }

    public static function __callStatic($method, array $arguments)
    {
        call_user_func_array([self::logger(), $method], $arguments);
    }
}
