<?php defined('_JEXEC') or die('Restricted access');


function _db($sql,$type='list',$limitstart=0, $limit=0,$id=null){
    _load('mysqli');
    $a = new CobaMySQLi();
    $res=$a->query($sql,$type,$limitstart,$limit,$id);
    $res->sql=$sql;
    return $res;
}
function _dbInsert($tableName,$arr=array(),$idName='id'){
    $res=(object)array('result'=>false,'errors'=>array());
    if(count($arr)){
        _load('mysqli');
        $a = new CobaMySQLi();

        $columns = '`'.implode("`, `",array_keys($arr)).'`';
        $values='';
        $cnt=0;
        foreach($arr as $val){
            $cnt++;
            $values.=(($cnt==1)?'':', ')._dbEscapeString($val,$a);
        }
        $sql="INSERT INTO `$tableName` ($columns) VALUES ($values)";
        $res=$a->query($sql,'insert');
        $res->sql=$sql;
        if(is_int($res->result) AND $res->result>0){
            $res->result=_db('SELECT * FROM `'.$tableName.'` WHERE `'.$idName.'` = '.$res->result,'row')->result;
        }
        return $res;
    }
    return $res;
}
function _dbUpdate($tableName,$arr=array(),$where=''){
    $res=(object)array('result'=>false,'errors'=>array());
    if(count($arr)){
        _load('mysqli');
        $a = new CobaMySQLi();

        if(strlen($where)){
            $where=' WHERE '.$where;
        }
        $sql="UPDATE `$tableName` SET ";
        $cnt=0;
        foreach($arr as $ind=>$val){
            $cnt++;
            $sql.=(($cnt==1)?'':', ').' `'.$ind.'` = '._dbEscapeString($val,$a);
        }

        $res=$a->query($sql.$where,'edit');
        $res->sql=$sql;
        if($res->result==true){
            $res->result=_db('SELECT * FROM `'.$tableName.'`'.$where,'row')->result;
        }
    }
    return $res;
}
function _dbDelete($tableName,$where=''){
    $sql='DELETE FROM `'.$tableName.'` '.((strlen($where))?('WHERE '.$where):'');
    _load('mysqli');
    $a = new CobaMySQLi();
    $res=$a->query($sql,'edit');
    return $res;
}
function _dbEscapeString($val,$obj=null){
    _load('mysqli');
    switch(gettype($val)){
        case 'NULL':
            return 'NULL';
        break;
        case 'string': case 'double': case 'integer':
            return "'".$obj->link->escape_string($val)."'";
        break;
        case 'boolean':
            return ($val==true)?'true':'false';
        break;
    }
    return '';
}
?>