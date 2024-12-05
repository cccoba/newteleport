<?php defined('_JEXEC') or die('Restricted access');

use Illuminate\Database\Eloquent\Model as Eloquent;
class RoleModel extends Eloquent
{
    protected $table = 'roles';
    protected $guarded = ['id'];
    protected $casts = [
        'params' => 'object',
    ];
    public $timestamps = false;
}
?>