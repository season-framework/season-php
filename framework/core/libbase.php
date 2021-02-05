<?php

namespace framework;

class LibBase
{

    private static $_data;
    private static $_dic;

    public $data;
    public $dic;

    public function __construct()
    {
        $this->data = &self::$_data;
        $this->dic = &self::$_dic;
    }

    private function module_path($type, $name)
    {
        $_name = explode("/", $name);
        if (strlen($_name[0]) == 0)
            array_shift($_name);
        $module_name = array_shift($_name);
        $_name = implode("/", $_name);
        $_path = PATH_MODULE . "/$module_name/$type/$_name.php";
        return $_path;
    }

    public function lib($name)
    {
        $fn_name = basename($name);
        $ns = explode("/", $name);
        $ns = implode("\\", $ns);

        // from module
        if ($fn_name != $ns) {
            $_path = $this->module_path("lib", $name);
            if (file_exists($_path)) {
                $ns = explode("/", $name);
                if (count($ns[0]) > 0) {
                    array_unshift($ns, "lib");
                    array_unshift($ns, "framework");
                }
                $ns = "\\" . implode("\\", $ns);
                require_once($_path);
                return new $ns;
            }
        }

        $_path = PATH_APP . "/lib/$name.php";
        if (file_exists($_path)) {
            require_once($_path);
            return new $fn_name;
        }

        $_path = PATH_CORE . "/lib/$name.php";
        if (file_exists($_path)) {
            require_once($_path);
            return new $fn_name;
        }

        throw new Exception(500, "Lib Not Found");
    }

    public function util($name)
    {
        $fn_name = basename($name);
        $ns = explode("/", $name);
        $ns = implode("\\", $ns);

        // from module
        if ($fn_name != $ns) {
            $_path = $this->module_path("util", $name);
            if (file_exists($_path)) {
                $ns = explode("/", $name);
                if (count($ns[0]) > 0) {
                    array_unshift($ns, "util");
                    array_unshift($ns, "framework");
                }
                $ns = "\\" . implode("\\", $ns);
                require_once($_path);
                return new $ns;
            }
        }

        // from app or core
        $_path = PATH_APP . "/util/$name.php";
        if (file_exists($_path)) {
            require_once($_path);
            return new $fn_name;
        }

        $_path = PATH_CORE . "/util/$name.php";
        if (file_exists($_path)) {
            require_once($_path);
            return new $fn_name;
        }

        throw new Exception(500, "Util Not Found");
    }

    public function model($name)
    {
        $fn_name = basename($name . "_model");
        $ns = explode("/", $name);
        $ns = implode("\\", $ns) . "_model";

        // from module
        if ($fn_name != $ns) {
            $_path = $this->module_path("model", $name);
            if (file_exists($_path)) {
                $ns = explode("/", $name);
                if (count($ns[0]) > 0) {
                    array_unshift($ns, "model");
                    array_unshift($ns, "framework");
                }
                $ns = "\\" . implode("\\", $ns) . "_model";
                require_once($_path);
                return new $ns;
            }
        }

        // from app core
        $_path = PATH_APP . "/model/$name.php";
        if (file_exists($_path)) {
            require_once($_path);
            return new $fn_name;
        }

        throw new Exception(500, "Model Not Found");
    }

    public static function getInstance()
    {
        return new self();
    }
}
