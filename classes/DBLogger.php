<?php

class DBLogger extends Logger
{
    protected $logException = false;

    protected $connectionName = '';
    protected $serverName = '';
    protected $serverIp = '';

    public function __construct($filename, $connection_name, $server_name)
    {
        parent::__construct($filename);
        $this->connectionName = $connection_name;
        $this->serverName = $server_name;
        $this->serverIp = (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : '?';
    }

    protected function compose($level, $message, array $context = [])
    {
        $res = date('[d-M-Y H:i:s e]')." [$level] [client {$this->clientIp}] {$this->serverName}:{$this->serverIp}:{$this->connectionName}".PHP_EOL
            .$message;
        if (!empty($context)) {
            $res .= PHP_EOL.$this->toString($context).PHP_EOL;

            if (!empty($context['exception']) && ($context['exception'] instanceof Exception)) {
                $backtrace = $this->getBacktrace($context['exception']);
                $padding = str_repeat(' ', 4);
                $res .= $padding.implode(PHP_EOL.$padding, $backtrace);
            }
        }
        return $res.PHP_EOL.PHP_EOL;
    }

    protected function getBacktrace(Exception $e)
    {
        $res = [];
        foreach ($e->getTrace() as $step) {
            if (!isset($step['file'])) continue;

            $res[] = $step['file'].':'.$step['line'].' '.(isset($step['class']) ? $step['class'].'::' : '').$step['function'];
        }
        return $res;
    }
}
