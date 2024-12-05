<?php defined('_JEXEC') or die('Restricted access');
 
 _load("**.services.users");
class CControllerUsers extends CController
{
    private $userService;
    function __construct()
    {
        parent::__construct(
            array(
                "json" => array(
                    "getMainData",
                    "getAll"
                ))
        );
        $this->userService = new UsersService();
    }

    function getMainData(){ 
        $res = $this->userService->getMainData();
        return $res;
    }
    function getAll(){ 
        $res = $this->userService->getAll();
        return $res;
    }
    
}