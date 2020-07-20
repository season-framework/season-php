<?php
namespace framework;

class Base extends LibBase {

    public $ui;
    public $request;
    public $response;
    public $config;

    public function __construct() {
        parent::__construct();
        $this->request = $this->lib("request"); 
        $this->response = $this->lib("response"); 
        $this->ui = $this->lib("ui"); 
        $this->config = $this->lib("config"); 
    }
}
