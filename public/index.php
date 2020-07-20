<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if ( file_exists( "./_include.php" ) ) include("./_include.php");
define('PATH_ROOT', dirname(dirname(__FILE__)));
if ( ! defined('PATH_BASE') ) define('PATH_BASE', dirname(dirname(__FILE__)));
if ( ! defined('PATH_APP') ) define('PATH_APP', PATH_BASE . '/app');
if ( ! defined('PATH_MODULE') ) define('PATH_MODULE', PATH_BASE . '/modules');
if ( ! defined('PATH_CORE') ) define('PATH_CORE', PATH_ROOT . '/framework');
if ( ! defined('PATH_VENDOR') ) define('PATH_VENDOR', PATH_BASE . '/vendor');

define('REQUEST_URI', $_SERVER['REQUEST_URI']);

if ( file_exists(PATH_VENDOR . '/autoload.php') )
    require_once(PATH_VENDOR . '/autoload.php');
require_once(PATH_CORE . '/autoload.php');

$process = new framework\Process;
$config = $process->lib("config");
$config->load();
define('BASEURL', $config->get('baseurl', ''));

$process->data = new stdClass;

$lang = $process->request->language();
$modules = $process->util("fs")->readdir(PATH_MODULE);

$process->dic = new stdClass; 
foreach ( $modules as $module ) {
    $dictfile = PATH_MODULE . "/$module/dictionary/$lang.php";
    $dictfile_default = PATH_MODULE . "/$module/dictionary/default.php";

    try {
        if ( file_exists( $dictfile ) ) {
            require_once($dictfile);
            foreach ( $dictionary as $dic => $val ) {
                $key = strtoupper("__DIC_$module"."_".$dic);
                define($key, $val);
                $process->dic->$key = $val;
            }
        }
    } catch(Exception $e) {
    }

    try {
        if ( file_exists( $dictfile_default ) ) {
            require_once($dictfile_default);
            foreach ( $dictionary as $dic => $val ) {
                $key = strtoupper("__DIC_$module"."_".$dic);
                if ( ! defined($key) ) {
                    define($key, $val);
                    $process->dic->$key = $val;
                }
            }
        }
    } catch(Exception $e) {
    }
}

try {
    $process->process();
} catch ( Exception $e ) {
    $process->response->error($e);
}
