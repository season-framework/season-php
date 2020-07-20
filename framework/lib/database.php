<?php

class database extends \framework\LibBase {

    private static $cache = array();

    private $config;

    public function __construct() {
        parent::__construct();
        $this->config = $this->lib("config");
        $this->config->load("database");
    }

    private function load_config($namespace, $key, $default = "") {
        $dbconfig = $this->config->get($namespace, array());
        if ( isset( $dbconfig[$key] ) )
            return $dbconfig[$key];
        return $default;
    }

    public function pdo($ns='system') {
        if ( isset ( self::$cache[$ns] ) ) 
            return self::$cache[$ns];

        $dbtype = $this->load_config($ns, "type", "mysql");
        $host = $this->load_config($ns, "host", "127.0.0.1");
        $user = $this->load_config($ns, "user", "root");
        $password = $this->load_config($ns, "password", "");
        $database = $this->load_config($ns, "database", "");
        $charset = $this->load_config($ns, "charset", "utf8");
        
        $dsn = "$dbtype:host={$host};dbname={$database};charset={$charset}";
        self::$cache[$ns] = new \PDO($dsn, $user, $password);
        return self::$cache[$ns];
    }
    
}
