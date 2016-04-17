<?php

use Database\Connection;
use Database\Grammar;
use Database\QueryBuilder;
use Docs\DB as DBDocs;

class DB
{
    use Singleton;
    use DBDocs;

    protected static $config = [];
    protected static $serverName = '';

    /**
     * @var Connection[]
     */
    protected $connections = [];

    public static function setUp(array $config, $server_name)
    {
        self::$config = $config;
        self::$serverName = $server_name;
    }

    /**
     * @param string $name
     * @return Connection
     */
    public static function connection($name = '')
    {
        if (empty($name)) {
            $name = self::$config['default'];
        }

        $db = self::getInstance();
        if (!isset($db->connections[$name])) {
            $logger = new DBLogger(self::$config['log'], $name, self::$serverName);
            $db->connections[$name] = new Connection(self::$config['connections'][$name], $logger);
        }
        return $db->connections[$name];
    }

    /**
     * @param $name
     * @param db_driver_mysql $CDB
     * @return QueryBuilder
     */
    public static function table($name, db_driver_mysql $CDB = null)
    {
        $connection = (!is_null($CDB)) ? self::extractConnection($CDB) : self::connection();
        return (new QueryBuilder($connection, new Grammar))->from($name);
    }

    /**
     * @see http://stackoverflow.com/a/3683868
     * @param string $str
     * @return string
     */
    public static function like($str)
    {
        return str_replace(['=', '%', '_'], ['==', '=%', '=_'], $str);
    }

    /**
     * :TODO: remove
     * @deprecated
     */
    public static function extractConnection(db_driver_mysql $CDB)
    {
        $cdb_params = $CDB->obj;
        $host = $cdb_params['sql_host'];
        $db_name = $cdb_params['sql_database'];
        $user = $cdb_params['sql_user'];
        $pass = $cdb_params['sql_pass'];
        return self::findConnection($host, $db_name, $user, $pass);
    }

    /**
     * :TODO: remove
     * @deprecated
     */
    public static function findConnection($host, $database, $username, $password)
    {
        foreach (self::$config['connections'] as $name => $config) {
            if (($config['host'] == $host)
                && ($config['database'] == $database)
                && ($config['username'] == $username)
                && ($config['password'] == $password)
            ) {
                return self::connection($name);
            }
        }

        throw new RuntimeException('Connection not found: host='.$host.'; database='.$database.'; username='.$username);
    }

    public static function __callStatic($method, array $arguments)
    {
        return call_user_func_array([self::connection(), $method], $arguments);
    }
}
