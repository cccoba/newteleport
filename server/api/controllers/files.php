<?php defined('_JEXEC') or die('Restricted access');
class CControllerFiles extends CController
{
    private $serviceName="files";
    function __construct()
    {
        parent::__construct(
            array(
                "json" => array(
                    "remove",
                    "add",
                )
                )
            ,
            "main",true
        );
    }

    function remove(){
        $res = $this->service($this->serviceName)->remove($this->getJsonData());
        return $res;
    }
    function add(){
        $res = $this->service($this->serviceName)->add($_FILES["formFile"]);
        return $res;
    }
}