<?php

class fs {
    
    public function extension( $path ) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function exists( $path ) {
        return file_exists( $path ); 
    }

    public function isdir( $path ) {
        return is_dir($path);
    }

    public function readdir ( $path , $hiddenfiles = false ) {
        $res = array();
        if ( $handle = opendir( $path ) ) {
            while ( ( $name = readdir( $handle ) ) !== false ) {
                if ( $name == '.' || $name == '..' ) continue;
                if ( $name[0] == '.' && $hiddenfiles == false ) continue;
                array_push($res, $name);
            }

            closedir( $handle );
        }

        return $res;
    }

    public function scandir ($dir) {
        $result = [];
        foreach ( scandir($dir) as $filename ) {
            if ($filename[0] === '.') continue;
            $filePath = $dir . '/' . $filename;
            if (is_dir($filePath)) {
                foreach ($this->scandir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } 
            else {
                $result[] = $filename;
            }
        }
        return $result; 
    }

    public function readfile ( $path ) {
        if ( $this->exists( $path ) == false ) {
            throw new ResponseException("File Not Found", 404);
        }

        $fp = fopen( $path , 'r' );
        $fr = fread( $fp , filesize($path) );
        fclose($fp);
        return $fr;  
    }

    public function mkdir ( $path ) {
        mkdir( $path , 0755, true );
    }

    public function write ( $path , $value ) {
        $fp = fopen( $path , 'w' );
        fwrite( $fp , $value );
        fclose( $fp );
    }

    public function unlink ( $path ) {
        if ( $this->exists( $path ) === false ) {
            return;
        }

        if ( is_dir( $path ) ) {
            $this->_rmdir( $path );
        } 
        else {
            unlink( $path );
        }
    }

    private function _rmdir( $dir ) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ( $objects as $object ) {
                if ( $object != "." && $object != ".." ) {
                    if ( filetype($dir."/".$object) == "dir" ) {
                        $this->_rmdir($dir."/".$object);
                    } 
                    else { 
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function join () {
        $args = func_get_args();
        $paths = array();
        foreach ($args as $arg) {
            $paths = array_merge($paths, (array)$arg);
        }

        $paths = array_map(function($p) { return trim($p, "/"); }, $paths);
        $paths = array_filter($paths);
        return '/' . join('/', $paths); 
    }
    
    public function mimetype( $filename ) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
       
        $extension = $this->extension($filename);
        if (array_key_exists($extension, $mime_types)) {
            return $mime_types[$extension];
        }
        return 'application/octet-stream';
    }
}
