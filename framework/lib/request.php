<?php

class request extends \framework\LibBase {

    public function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function client_ip() {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } 
        else if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        return $ip;
    }

    public function language() {
        if ( isset($_COOKIE["FRAMEWORKLANG"]) ) {
            return $_COOKIE["FRAMEWORKLANG"];
        }

        if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
            return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        return "en";
    }

    public function uri() {
        return $this->util('uri')->request_uri(); 
    } 

    public function match($pattern, $flags = 0) {
        return $this->util('uri')->match($pattern, $this->uri(), $flags); 
    }

    public function query_check($name, $value = null) {
        $q = $this->query($name, true);
        if ( ! $q || $q == "" || count($q) == 0 )
            throw new \framework\Exception(400);
        if( $value && $value != $q )
            throw new \framework\Exception(400);
    }

    public function query($name=null, $required=False) {
        $primary = $_GET;
        $secondary = $_POST;
        if ( $this->method() == 'POST' ) {
            $primary = $_POST;
            $secondary = $_GET;
        }

        if ( $name != null ) {
            if ( isset( $primary[$name] ) ) {
                return $primary[$name];
            }

            if ( isset( $secondary[$name] ) ) {
                return $secondary[$name];
            }

            if ( $required === True ) {
                throw new \framework\Exception(400);
            }
            return $required;
        }

        $result = array();
        foreach ( $secondary as $key => $value ) {
            if ( $value !== null )
                $result[$key] = $value;
        }
        foreach ( $primary as $key => $value ) {
            if ( $value !== null)
                $result[$key] = $value;
        }
        return $result;
    }

}
