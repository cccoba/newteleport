<?php defined('_JEXEC') or die('Restricted access');
_load("**.helper");
class CControllerTest extends CController
{
    function __construct()
    {
        parent::__construct(
            array(
                "json" => array(
                    "test",
                ),
                "html"=>[
                    "qrGenerate",
                ]
            ),
            "main",true
        );
    }

    function test(){
        $res = new jsonClass();
        _p(getAuthToken());
        return $res;
    }
    
}