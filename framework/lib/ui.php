<?php

class ui extends \framework\LibBase {

    public static $ui_data = array();
    public static $view = array();
    public static $layout = null;
    public static $error_layout = null;

    public function set_error_layout($uri) {
        self::$error_layout = $uri;
    }

    public function set_layout($uri) {
        self::$layout = $uri;
    }

    public function set_view($namespace, $uri) {
        self::$view[$namespace] = $uri;
    }

    public function set_data($key, $value) {
        self::$ui_data[$key] = $value;
    }

    public function get_error_layout() {
        $layout = self::$error_layout;
        if ( $layout == null ) return null;
        if ( $this->util("str")->startswith($layout, "/") ) {
            $layout = substr( $layout, 1 );
        }
        return $layout;
    }

    public function get_layout() {
        $layout = self::$layout;
        if ( $layout == null ) return null;
        if ( $this->util("str")->startswith($layout, "/") ) {
            $layout = substr( $layout, 1 );
        }
        return $layout;
    }

    public function get_view($namespace) {
        if ( isset( self::$view[$namespace] ) == false ) {
            return null;
        } 
        
        $view = self::$view[$namespace];
        if ( $this->util("str")->startswith($view, "/") ) {
            $view = substr( $view, 1 );
        }

        return $view;
    }

    public function get_data($key, $default=null) {
        if ( isset( self::$ui_data[$key] ) ) {
            return self::$ui_data[$key];
        }
        return $default;
    }

    public function script($valname, $val) {
        $val = json_encode($val);
        return "<script>$valname = $val</script>";
    }
}
