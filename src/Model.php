<?php

namespace Kpf;

use MySQLiLib\MySQLDb;

class Model extends MySQLDb
{
    protected $connection = null;

    /**
     * Model constructor.
     * @param array $options [host,user,password,database,port]
     */
    public function __construct($options = [])
    {
        if(!empty($options['host'])){
            $host = $options['host'];
            $user = $options['user'];
            $password = $options['password'];
            $dbName = $options['database'];
            $port = $options['port'];
            $this->connection = $this->connect($host, $user, $password, $dbName, $port);
        }
    }

    /**
     * @param string $configName
     * @throws Exception\ConfigException
     * @throws \MySQLiLib\Exception
     */
    public function select(string $configName = Constant::COMMON_DB)
    {
        $options = Application::getConfig()->load($configName);
        if(!empty($options) && !empty($options['host'])){
            $host = $options['host'];
            $user = $options['user'];
            $password = $options['password'];
            $dbName = $options['database'];
            $port = $options['port'];
            $this->connection = $this->connect($host, $user, $password, $dbName, $port);
        }
    }
}