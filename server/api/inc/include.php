<?php defined('_JEXEC') or die('Restricted access');
if (!defined('_c_include_all')) {
    if (!defined('DS')) {
        define('DS', DIRECTORY_SEPARATOR);
    }
    function _load($name,$debug=false)
    {
        if($debug){
            echo "******start load $name**********<br />";
        }
        $name = str_replace('.', DS, $name);
        $name = str_replace('**', '..', $name);
        if($debug){
            echo "name = $name<br />";
            echo "realpath = ".realpath(_incPath . DS . $name . ".php")."<br />";
        }
        $filename = realpath(_incPath . DS . $name . ".php");
        if (!defined('_c_include_' . $name)) {
            require_once $filename;
            define('_c_include_' . $name, '1');
        }
        if($debug){
            echo "******end load $name**********<br />";
        }
    }
    function _loadAll($name){
        $name = str_replace('.', DS, $name);
        $name = str_replace('**', '..', $name);
        $dir = realpath(_incPath . DS.$name);
        
        if (!defined('_c_includeall_' . $name)) {
            $phpFiles = glob($dir . "/*.php");
            if(count($phpFiles)){
                foreach ($phpFiles as $filename) {
                    require_once $filename;
                }
            }
            define('_c_includeall_' . $name, '1');
        }
    }
    require_once realpath ("./../../"). '/vendor/autoload.php';

    define("_incPath", dirname(__FILE__));
    define("_AppVersion", "0.0.1");
    define("_BasePath","/gusislugi");
    define("_BaseSiteUrl","https://teleport-games.ru");
    _load('pswd');
    _load('comfort');
    _load('j');
    _load("**.interfaces.index");
    _load('eloquent.index');
    _load('automapper');
    _load('mvc.service.service');
    _load('mvc.controller.controller');

    define('_c_include_all', '1');
}
?>