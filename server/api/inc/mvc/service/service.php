<?php
defined('_JEXEC') or die('Restricted access');

function getService($tableName, $prefix = "Service")
{
    $path = realpath("services" . DS . $tableName . ".php");
    if ($path) {
        include_once($path);
        $tableClass = ucfirst($tableName).$prefix;
        if (class_exists($tableClass)) {
            return new $tableClass();
        }
    }
    return null;
}

class CService
{
    private $models = [];
    public $mapper;
    function __construct() {
        $this->mapper = getMapper();
    }
    function model($name){
        /*if(!isset($this->models[$name])){
            $this->models[$name] = getModel($name);
        }*/
        return $this->models[$name];
    }
}