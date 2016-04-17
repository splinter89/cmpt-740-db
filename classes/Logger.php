<?php

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    protected $supportedLevels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];
    protected $logException = true;

    protected $destination = '';
    protected $clientIp = '';

    public function __construct($filename)
    {
        if (empty($filename) || !file_exists($filename) || !is_writable($filename)) {
            throw new InvalidArgumentException('Invalid filename: '.$filename);
        }

        $this->destination = $filename;
        $this->clientIp = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'cli';
    }

    public function log($level, $message, array $context = [])
    {
        $this->checkLevel($level);

        $record = $this->compose($level, $message, $context);
        $this->write($record);
    }

    protected function checkLevel($level)
    {
        if (!in_array($level, $this->supportedLevels)) {
            throw new InvalidArgumentException('Unknown log level: '.$level);
        }
    }

    protected function compose($level, $message, array $context = [])
    {
        $res = date('[d-M-Y H:i:s e]')." [$level] [client {$this->clientIp}] $message";
        if (!empty($context)) {
            $res .= PHP_EOL.$this->toString($context);
        }
        return $res.PHP_EOL;
    }

    protected function toString($data, $level = 0)
    {
        if (is_callable($data)) return '(callable)';
        if (is_resource($data)) return '(resource: '.get_resource_type($data).')';
        if (is_object($data)) return str_replace("\n", "\n".str_repeat(' ', 8), print_r($data, true));
        if (!is_array($data)) return json_encode($data, JSON_UNESCAPED_UNICODE);

        $spacer = str_repeat(' ', 4 * ($level + 1));

        $res = [];
        foreach ($data as $k => $v) {
            if (($k === 'exception') && !$this->logException) continue;

            $res[] = $spacer."[$k] => ".$this->toString($v, $level + 1);
        }
        return ($level > 0 ? PHP_EOL : '').implode(PHP_EOL, $res);
    }

    protected function write($record)
    {
        file_put_contents($this->destination, $record, FILE_APPEND);
    }
}
