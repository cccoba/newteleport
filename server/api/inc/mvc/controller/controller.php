<?php defined('_JEXEC') or die('Restricted access');

function getController($modelName, $prefix = "CController")
{
    $path = realpath("controllers" . DS . $modelName . ".php");
    if ($path) {
        include_once($path);
        $modelClass = $prefix . ucfirst($modelName);
        if (class_exists($modelClass)) {
            return $modelClass;
        }
    }
    return null;
}

class jsonClass
{
    var $result;
    var $error;
    function __construct($result = "notInit")
    {
        if ($result === "notInit") {
            $this->result = new stdClass();
        }
        else{
            $this->result = $result;
        }
        $this->error = "";
    }
}
class CController
{
    private $actions = array(
        'html' => array("main"),
        "json" => array()
    );
    public $services=[];
    public $action = "main";
    public $format = "json";
    public $withLog = false;

    function __construct($newActions = [], $defaultAction = "main",$addSetHeader=true)
    {
        if (count($newActions)) {
            $this->actions = $newActions;
            
        }
        $v = getGet("view",$defaultAction);
        $this->action = $defaultAction;
        
        if ($v !== $defaultAction) {
            $v = trim($v);
            if (isset($this->actions["html"]) && in_array($v, $this->actions["html"])) {
                $this->action = $v;
                $this->format = "html";
            } else if (isset($this->actions["json"]) && in_array($v, $this->actions["json"])) {
                $this->action = $v;
                $this->format = "json";
            }
        }
        if($addSetHeader){
            $this->setHeader();
        }
    }

    function log($name, $data, $filename = "file.txt")
    {
        if ($this->withLog) {
            addLog($name, $data, $filename);
        }
    }
    function run()
    {
        if($_SERVER['REQUEST_METHOD']=="OPTIONS"){
            return null;
        }
        $action = $this->action;
        if ($this->format === "json") {
            header('Content-type: application/json');
            echo json_encode($this->$action());
        } else {
            echo $this->$action();
        }
    }
    function main()
    {
        $res = new jsonClass(false);
        $res->error="replace me";
        return $res;
    }
    function setHeader()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    }
    function getJsonData()
    {
        $content = (file_get_contents('php://input'));
        $data = json_decode(strlen($content) > 0 ? $content : "{}", true);
        return $data;
    }
    function getService(string $name){
        if(!isset($this->services[$name])){
            $this->services[$name] = getService($name);
        }
        return $this->services[$name];
        
    }
}

?>