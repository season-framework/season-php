<?php

class uri {
 
    const FNM_PATHNAME = 1;
    const FNM_NOESCAPE = 2;
    const FNM_PERIOD = 4;
    const FNM_CASEFOLD = 16;

    private function starts_with($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    public function request_uri($baseuri = null) {
        $content = strtok( $_SERVER["REQUEST_URI"] , "?" );
        $from = '/'.preg_quote($baseuri, '/').'/';
        $to = "";
        $result = str_replace( "//" , "/" , "/".preg_replace($from, $to, $content, 1) );

        if ( BASEURL != "" && $this->starts_with($result, BASEURL) ) {
            $result = substr($result, strlen(BASEURL));
        }

        if ( ! $this->starts_with($result, "/") ) {
            $result = "/$result";
        }

        return $result;
    }

    public function join() {
        $args = func_get_args();
        $paths = array();
        foreach ($args as $arg) {
            $paths = array_merge($paths, (array) $arg);
        }
        $paths = array_map(function($p) { return trim($p, "/"); }, $paths);
        $paths = array_filter($paths);
        return join('/', $paths); 
    }

    public function match($pattern, $string, $flags = 0) { 
        return $this->pcre_fnmatch($pattern, $string, $flags); 
    }

    private function pcre_fnmatch($pattern, $string, $flags = 0) { 
        $modifiers = null; 
        $transforms = array( 
            '\*'    => '.*', 
            '\?'    => '.', 
            '\[\!'    => '[^', 
            '\['    => '[', 
            '\]'    => ']', 
            '\-'    => '-', 
            '\.'    => '\.', 
            '\\'    => '\\\\' 
        ); 
        
        if ($flags & self::FNM_PATHNAME) { 
            $transforms['\*'] = '[^/]*'; 
        } 
        
        if ($flags & self::FNM_NOESCAPE) { 
            unset($transforms['\\']); 
        } 
        
        if ($flags & self::FNM_CASEFOLD) { 
            $modifiers .= 'i'; 
        } 
        
        if ($flags & self::FNM_PERIOD) { 
            if (strpos($string, '.') === 0 && strpos($pattern, '.') !== 0) return false; 
        } 
        
        $pattern = '#^' 
            . strtr(preg_quote($pattern, '#'), $transforms) 
            . '$#' 
            . $modifiers;
        return (boolean) preg_match($pattern, $string); 
    } 

}
