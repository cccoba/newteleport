<?php defined('_JEXEC') or die('Restricted access');
class CControllerCRUD extends CController
{
    public $serviceName="";
    function __construct($newActions = [],$serviceName)
    {
        $actions = array(
            "json" => array(
                "list","update","remove","record","save"
            )
        );
        if(count($newActions)){
            if(count($newActions["json"])){
                $actions["json"] = array_merge($actions["json"],$newActions["json"]);
            }
            if(count($newActions["html"])){
                $actions["html"] = array_merge($actions["html"],$newActions["html"]);
            }
            
        }
        $this->serviceName=$serviceName;
        parent::__construct($actions,"main",true);
        
    }
    function list(){
        $res = $this->service($this->serviceName)->list();
        return $res;
    }
    function update(){
        $res = $this->service($this->serviceName)->update($this->getJsonData());
        return $res;
    }
    function remove(){
        $res = $this->service($this->serviceName)->remove($this->getJsonData());
        return $res;
    }
    function record(){
        $res = $this->service($this->serviceName)->record(intval($_GET["id"]));
        return $res;
    }
    function save(){
        $res = $this->service($this->serviceName)->save($this->getJsonData());
        return $res;
    }
}
?>