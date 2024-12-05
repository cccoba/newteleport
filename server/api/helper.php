<?php defined('_JEXEC') or die;

class helper
{
    static function getCurrentUser(){
        $token = getAuthToken();
        if($token){
            $user = userModel::with("role")->where('hash',$token)->first();
            if($user){
                return $user->toArray();
            }
        }
        return null;
    }
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