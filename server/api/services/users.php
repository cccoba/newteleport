<?php defined('_JEXEC') or die('Restricted access');
_load("**.helper");
class UsersService  extends CService
{
    private $user;
    public function __construct() {
        parent::__construct();
        $this->user = helper::getCurrentUser();
    }
    public function getAll(){
        $res = new jsonClass([]);
        $result =  UserModel::with("role")->get()->toArray();
        if($result!=null){
            foreach ($result as $user) {
                $item = $this->userDtoMapping($user);
                $res->result[] = $item;
            }
            
        }
        return $res;
    }
    public function getMainData(){

        $res = new jsonClass();
        $res->result = (object)array(
            "user"=>$this->user?$this->userDtoMapping($this->user):null,
            "appVersion"=>_AppVersion
        );
       
        return $res;
    }

    private function userDtoMapping($user){
        return $this->mapper->map($user,"UserDto",["role"=>function($x){return $this->mapper->map($x->role,"RoleDto");}]);
    }
}
?>