<?php

function framework_autoload( $className ) {
    $className = strtolower($className);
    $elements = explode('\\', $className);
    if (array_shift($elements) !== 'framework') return;
    if (count($elements) === 0) return;
    
    $file = PATH_APP . '/' . implode('/', $elements) . '.php';
    $file = strtolower($file);
    if ( file_exists($file) ) {
        require_once($file);
        return;
    }

    $file = PATH_CORE . '/core/' . implode('/', $elements) . '.php';
    $file = strtolower($file);
    if ( file_exists($file) ) {
        require_once($file);
        return;
    }
}

spl_autoload_register('framework_autoload');
