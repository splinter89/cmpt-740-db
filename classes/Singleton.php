<?php

trait Singleton
{
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    /**
     * @return static
     */
    private static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new static;
        }
        return $instance;
    }
}
