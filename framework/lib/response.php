<?php

class response extends \framework\LibBase {

    public function language($lang) {
        setcookie("FRAMEWORKLANG", $lang, 0, '/');
    }

    public function redirect($uri) {
        if ( BASEURL != null ) 
            $uri = $this->util("fs")->join(BASEURL, $uri);
        header("Location: $uri");
        exit();
    }

    public function send($message, $content_type='text') {
        header("Content-Type: $content_type");
        echo $message;
        exit();
    }

    public function json($obj) {
        header("Content-Type: application/json");
        echo json_encode($obj);
        exit();
    }

    public function error($e) {
        $layout = $this->lib("ui")->get_error_layout();
        $layout = explode("/", $layout);

        $this->data->error = $e;

        try {
            http_response_code($e->getCode());
        } catch (Exception $e) {
        }

        $module_name = array_shift($layout);
        $layout_uri = implode("/", $layout);
        $layout_uri = PATH_MODULE . "/$module_name/view/$layout_uri.php";

        if ( file_exists( $layout_uri ) ) {
            global $view;
            $view = new \framework\View;
            include_once($layout_uri);
            exit();
        }

        header("Content-Type: application/json");
        echo json_encode( array(
            "code" => $e->getCode(),
            "message" => $e->getMessage(),
            "stacktrace" => "$e",
        ) );
    }

    public function render() {
        $layout = $this->lib("ui")->get_layout();
        $layout = explode("/", $layout);

        $module_name = array_shift($layout);
        $layout_uri = implode("/", $layout);
        $layout_uri = PATH_MODULE . "/$module_name/view/$layout_uri.php";
        
        if ( file_exists( $layout_uri ) ) {
            global $view;
            $view = new \framework\View;
            include_once($layout_uri);
            exit();
        }

        throw new \framework\Exception(404);
    }

    public function download($filename, $file=null) {
       $fs = $layout = $this->util("fs");
        if ( $fs->exists($filename) ) {
            $ext = $fs->extension($filename);
            if ( $ext == "php" ) {
                require_once($filename);
                exit();
            }

            $content_type = $fs->mimetype($filename);
            header("Content-Type: $content_type");
            if ( $content_type == "application/octet-stream" ) {
                if ( $file == null ) {
                    header("Content-Disposition: attachment; file=" . basename($filename));
                } else {
                    header("Content-Disposition: attachment; file=" . basename($file));
                }
            }
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($filename));
            readfile($filename);
            exit();
        }
        
        throw new \framework\Exception(404);
    }
}
