<?php define('_JEXEC', 1);
require_once 'inc' . DIRECTORY_SEPARATOR . 'include.php';

$controllerName = getGet("model");
if(strlen($controllerName)){
    
    $c = getController($controllerName);
    if($c!==null){
        $a = new $c;
        $a->run();
    }
}
?>