<?php
namespace framework;

class Process extends Base {

    public function process() {
        $this->modules = $this->util("fs")->readdir(PATH_MODULE);

        $this->resource();
        $this->filter();
        $this->module();
    }
  
    private function resource() {
        $uri = explode( "/", $this->request->uri() );
        array_shift($uri); // remove empty

        $module_name = array_shift($uri);
        if ( $module_name != "resources") return;

        $module_name = $uri[0];
        if ( in_array( $module_name, $this->modules ) ) {
            array_shift($uri);
            $uri = implode("/", $uri);
            $resource_uri = PATH_MODULE . "/$module_name/resources/$uri";
        } else {
            $uri = implode("/", $uri);
            $resource_uri = PATH_BASE . "/resources/$uri";
        }

        if ( ! file_exists($resource_uri) ) {
            throw new Exception(404);
        }

        $this->response->download($resource_uri);
        exit();
    }
 
    private function filter() {
        $this->config->load();
        $filters = $this->config->get("filter", array());

        foreach ( $filters as $filter ) {
            $filterpath = PATH_APP . "/filter/" . strtolower($filter) . ".php";
            if ( file_exists( $filterpath ) ) {
                require_once($filterpath);
                $filtercls = new $filter();
                $filtercls->process();
            } else {
                throw new Exception(500);
            }
        }
    }

    private function _load_default_module($module_name, $uri_org) {
        if ( in_array( $module_name, $this->modules ) ) {
            $uri = json_decode(json_encode($uri_org), true);
            $controller_uri = "index";
            $controller_path = PATH_MODULE . "/$module_name/controller/$controller_uri.php";
            $controller_name = "index_controller";

            if ( file_exists( $controller_path ) ) {
                require_once($controller_path);
                $controller = new $controller_name;
                $fn = "main";
                if ( count ( $uri ) > 0 ) {
                    if ( method_exists( $controller, $uri[0] ) ) {
                        $fn = array_shift($uri);
                    }
                }

                if ( method_exists ( $controller, $fn ) ) {
                    $segment = $this->lib("segment");
                    $segment->_load_uri($uri);
                    $controller->{$fn}($segment);
                }
            }
        }
    }

    private function _load_module($module_name, $uri_org) {
        if ( ! in_array( $module_name, $this->modules ) ) {
            return;
        }

        $uri = json_decode(json_encode($uri_org), true);
        $_uri = array();
        while ( True ) {
            $u = array_shift($uri);
            $_uri[] = $u;
            $controller_uri = strtolower( implode( "/", $_uri ) );
            $controller_path = PATH_MODULE . "/$module_name/controller/$controller_uri.php";
            $controller_name = strtolower( $_uri[ count($_uri) - 1 ] ) . "_controller";

            if ( file_exists( $controller_path ) ) {
                require_once($controller_path);
                $controller = new $controller_name;
                $fn = "main";
                if ( count ( $uri ) > 0 ) {
                    if ( method_exists( $controller, $uri[0] ) ) {
                        $fn = array_shift($uri);
                    }
                }

                if ( method_exists ( $controller, $fn ) ) {
                    $segment = $this->lib("segment");
                    $segment->_load_uri($uri);
                    $controller->{$fn}($segment);
                }
            }

            if ( count($uri) === 0 ) {
                break;
            }
        }
    }

    private function module() {
        $uri = explode( "/", $this->request->uri() );
        array_shift($uri); // remove empty

        $module_name = array_shift($uri);

        $this->_load_module($module_name, $uri);
        $this->_load_default_module($module_name, $uri);

        throw new Exception(404); 
    }
}
