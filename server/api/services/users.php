<?php defined('_JEXEC') or die('Restricted access');
_load("**.helper");
class UsersService  extends CService
{
    public function __construct() {
        parent::__construct();
    }
    public function getAll(){
        $res = new jsonClass([]);
        $result =  UserModel::with("role")->get()->toArray();
        if($result!=null){
            foreach ($result as $user) {
                $item = $this->mapper->map($user,"UserDto",["role"=>function($x){return $this->mapper->map($x->role,"RoleDto");}]);
                $res->result[] = $item;
            }
            
        }
        return $res;
    }
    /**
     * получение всех необходимых данных для пользователя сайта
     */
    public function getMainData(){

        $res = new jsonClass();
        $res->result = (object)array(
            "token"=>getAuthToken(),
            "user"=>null,
            "appVersion"=>_AppVersion
        );
       
        return $res;
    }
}
?>