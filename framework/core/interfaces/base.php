<?php
namespace framework\interfaces;

class Base extends \framework\Base {

    protected function pattern( $pattern ) {
        $uri = $this->request->uri();
        return $this->util->uri->match($uri, $pattern);
    }

    protected function set_header($value) {
        $this->ui->set_header($value);
    }

    protected function set_footer($value) {
        $this->ui->set_footer($value);
    }

    protected function check_method($method) {
        if ( strtolower($method) != strtolower($this->request->method()) ) {
            throw new \framework\Exception(405);
        }
    }
}
