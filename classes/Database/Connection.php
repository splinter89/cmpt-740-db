<?php

namespace Database;

use Psr\Log\LoggerInterface;

class Connection
{
    /**
     * @var array
     */
    protected $config;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \PDO
     */
    public $pdo;
    protected $fetchStyle = \PDO::FETCH_ASSOC;

    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->connect();
    }

    protected function connect()
    {
        $dsn = "{$this->config['driver']}:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
        $user = $this->config['username'];
        $pass = $this->config['password'];
        $options = [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION];

        try {
            $this->pdo = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Status: 503 Service Temporarily Unavailable');
            $this->logger->alert('Database unavailable. '.$e->getMessage());
            exit;
        }
    }

    public function reconnect()
    {
        $this->pdo = null;
        $this->connect();
    }

    public function setFetchStyle($fetch_style)
    {
        $allowed = [
            \PDO::FETCH_ASSOC,
            \PDO::FETCH_OBJ,
        ];
        if (in_array($fetch_style, $allowed)) {
            $this->fetchStyle = $fetch_style;
        }
    }

    /**
     * @return int
     */
    public function getFetchStyle()
    {
        return $this->fetchStyle;
    }

    /**
     * @param \PDOStatement $s
     * @param array $bindings
     * @return bool
     */
    protected function executeStatement(\PDOStatement $s, array $bindings = [])
    {
        try {
            $res = $s->execute($bindings);
        } catch (\PDOException $e) {
            $res = false;

            //list($sql_state, $error_code, $error_message) = $s->errorInfo();
            $context = [
                'exception' => $e,
                'query' => $s->queryString,
            ];
            if (!empty($bindings)) {
                $context['bindings'] = $bindings;
            }
            $this->logger->error($e->getMessage(), $context);
        }
        return $res;
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return bool
     */
    public function statement($query, array $bindings = [])
    {
        $s = $this->pdo->prepare($query);
        return $this->executeStatement($s, $bindings);
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function select($query, array $bindings = [])
    {
        $s = $this->pdo->prepare($query);
        $this->executeStatement($s, $bindings);
        return $s->fetchAll($this->fetchStyle) ?: [];
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return string The row ID of the last row that was inserted into the database
     */
    public function insert($query, array $bindings = [])
    {
        $s = $this->pdo->prepare($query);
        $this->executeStatement($s, $bindings);
        return $this->pdo->lastInsertId();
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function update($query, array $bindings = [])
    {
        $s = $this->pdo->prepare($query);
        $this->executeStatement($s, $bindings);
        return (int)$s->rowCount();
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return int
     */
    public function delete($query, array $bindings = [])
    {
        $s = $this->pdo->prepare($query);
        $this->executeStatement($s, $bindings);
        return (int)$s->rowCount();
    }
}
