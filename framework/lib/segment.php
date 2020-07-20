<?php

class segment extends \framework\LibBase {

    private $segment_base_uri;

    public function _load_uri($uri) {
        $this->segment_base_uri = $uri;
    }

    public function get($key, $required = null) {
        if ( is_numeric($key) ) {
            if ( isset( $this->segment_base_uri[$key] ) ) {
                return urldecode($this->segment_base_uri[$key]);
            }
        }

        $key = "$key";
        $key_loc = count($this->segment_base_uri);
        for ( $i = 0 ; $i < count($this->segment_base_uri) ; $i++ ) {
            $k = $this->segment_base_uri[$i];
            if ( $k == $key ) {
                $key_loc = $i;
            }
            if ( $i > $key_loc ) {
                return urldecode($k);
            }
        }

        if ( $required === True ) 
            throw new \framework\Exception(400);

        return $required;
    }

}
