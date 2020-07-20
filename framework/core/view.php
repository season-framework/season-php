<?php
namespace framework;

class View extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function load($namespace) {
        $view_uri = $this->ui->get_view($namespace);
        $view_uri = explode("/", $view_uri);

        $module_name = array_shift($view_uri);
        $view_uri = implode("/", $view_uri);
        $view_uri = PATH_MODULE . "/$module_name/view/$view_uri.php";
        
        if ( file_exists( $view_uri ) ) {
            global $view;
            $view = new \framework\View;
            include_once($view_uri);
            return;
        }

        $view_uri = $namespace;
        $view_uri = explode("/", $view_uri);

        $module_name = array_shift($view_uri);
        $view_uri = implode("/", $view_uri);
        $view_uri = PATH_MODULE . "/$module_name/view/$view_uri.php";
        
        if ( file_exists( $view_uri ) ) {
            global $view;
            $view = new \framework\View;
            include_once($view_uri);
            return;
        }
    }

    public function uri($uri) {
        return $this->util("fs")->join(BASEURL, $uri);
    } 

}
