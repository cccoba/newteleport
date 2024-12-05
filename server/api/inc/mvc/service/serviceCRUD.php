<?php defined('_JEXEC') or die('Restricted access');
abstract class CServiceCRUD extends CService
{
    public $modelName="";
    public function __construct($modelName) {
        $this->modelName=$modelName;
        parent::__construct();
    }
    abstract function list();
    abstract function remove($ids=[]);
    abstract function record(int $id);
    abstract function save($data);
}