<?php defined('_JEXEC') or die('Restricted access');
use Illuminate\Database\Eloquent\Model as Eloquent;
class UserModel extends Eloquent
{
    protected $table = 'users';
    protected $guarded = ['id',"created_at","updated_at"];
    public $timestamps = true; 

    public function role()
    {
        return $this->belongsTo(RoleModel::class,"roleId","id");
    }
}
?>