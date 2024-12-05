<?php defined('_JEXEC') or die('Restricted access');

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\AutoMapper;

function getInterface($modelName)
{
    $path = realpath("interfaces" . DS . $modelName . ".php");
    if ($path) {
        include_once($path);
        if (class_exists($modelName)) {
            return new $modelName();
        }
    }
    return null;
}
function getMapper(){
    return new CMapper();
}

class CMapper{
    private $config;
    private $automapper;
    private $registeredMapping=[];
    function __construct() {
        $this->config  = new AutoMapperConfig();
        $this->registeredMapping = array();
    }
    function registerMapping(string $classSourceName,string  $classResultName,$members=[]){
        if (class_exists($classResultName) 
            && class_exists($classSourceName) 
            &&!in_array($classSourceName."->".$classResultName,$this->registeredMapping)
        ){
            $mapping = $this->config->registerMapping($classSourceName, $classResultName);
            if (isset($members) && count($members)) {
                foreach ($members as $fieldName => $value) {
                    $mapping->forMember($fieldName,$value);
                }
            }
            $this->registeredMapping[]=$classSourceName."->".$classResultName;

            $this->automapper  =  new AutoMapper($this->config);
            return true;
        }
        return false;
    }
    public function map($classFrom,string $classResultName,$members=[]){
        $this->registerMapping("stdClass",$classResultName,$members);
        $result = $this->automapper->map((object)$classFrom,$classResultName);
        return $result;
    }
    public function mapArray($arrayFrom,string $classResultName,$members=[]){
        $this->registerMapping("stdClass",$classResultName,$members);
        $result = $this->automapper->mapMultiple($arrayFrom,$classResultName);
        return $result;
    }
}
?>