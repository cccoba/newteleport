<?php defined('_JEXEC') or die('Restricted access');
use Illuminate\Database\Capsule\Manager as Capsule;

_load("pswd");
_loadAll("**.models");

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => "mysql",
    'host'      => pswd::get("bd.host"),
    'database'  => pswd::get("bd.db"),
    'username'  => pswd::get("bd.user"),
    'password'  => pswd::get("bd.password"),
    'prefix'    => pswd::get("bd.prefix"),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
$firstInitDatabase = true;

function parseDBErrorData(\Throwable $e) {
    $errorCode = $e->errorInfo[1];
    if ($errorCode == 1062) {
        return jt("DB.ERROR1062");
    }
    return jt("DB.ERROR");
}

if($firstInitDatabase){

    if(!Capsule::schema()->hasTable("roles")){
        Capsule::schema()->create('roles', function ($table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description');
            $table->json('params');
        });
    }
    
    if(!Capsule::schema()->hasTable("users")){
        Capsule::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('hash')->unique();
            $table->integer('roleId')->unsigned()->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->boolean('isAdult');
            $table->boolean('agreementApp');
            $table->string('password');
            $table->string('registrationType');
            $table->integer('status')->unsigned();
            
            $table->timestamps();
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }
}
?>