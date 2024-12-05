<?php defined('_JEXEC') or die;

class helper
{
    static function checkPermission($permissionName,$params,$withView=true){
        if(isset($params->$permissionName)){
            if($params->$permissionName!=="no"){
                if($withView){
                    return true;
                }
                else{
                    return $params->$permissionName==="edit";
                }
            }
        }
        return false;
    }
}