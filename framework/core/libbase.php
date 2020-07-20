<?php
namespace framework;

class LibBase {

    private static $_data;
    private static $_dic;

    public $data;
    public $dic;

    public function __construct() {
        $this->data = & self::$_data; 
        $this->dic = & self::$_dic; 
    }

    public function lib($name) {
        $_path = PATH_APP . "/lib/$name.php";

        if ( file_exists($_path) ) {
            require_once($_path);
            $name = basename($name);
            return new $name;
        }

        $_path = PATH_CORE . "/lib/$name.php";
        if ( file_exists($_path) ) {
            require_once($_path);
            $name = basename($name);
            return new $name;
        }

        throw new Exception(500, "Lib Not Found"); 
    }

    public function util($name) {
        $_path = PATH_APP . "/util/$name.php";

        if ( file_exists($_path) ) {
            require_once($_path);
            $name = basename($name);
            return new $name;
        }

        $_path = PATH_CORE . "/util/$name.php";
        if ( file_exists($_path) ) {
            require_once($_path);
            $name = basename($name);
            return new $name;
        }

        throw new Exception(500, "Util Not Found"); 
    }

    public function model($name) {
        $_path = PATH_APP . "/model/$name.php";

        if ( file_exists($_path) ) {
            require_once($_path);
            $name = basename($name . "_model");
            return new $name;
        }
        
        throw new Exception(500, "Model Not Found");
    }

    public static function getInstance() {
        return new self();
    }

}
