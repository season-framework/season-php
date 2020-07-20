<?php

class config extends \framework\LibBase {
    private static $cache = array();

    private $config_data = array();
    private $name = "config";

    public function get( $key, $default = null ) {
        if ( isset ( $this->config_data[$key] ) ) 
            return $this->config_data[$key];
        return $default;
    }

    public function load($name="config") {
        $config_path = PATH_APP . "/config/$name.php";

        if ( ! isset( self::$cache[$name] ) ) {
            if ( file_exists( $config_path ) ) {
                require_once($config_path);
                self::$cache[$name] = $config;
            }
        }

        $this->name = $name;
        $this->config_data = self::$cache[$name];
        return $this;
    }
}
