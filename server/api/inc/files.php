<?php defined('_JEXEC') or die('Restricted access');
class CFiles{
    var $base_path;
    var $uid;
    var $files;
    var $files_format;
    var $files_size;
    var $params;
    var $dir;
    function __construct(
                $format=array('pdf','jpeg','jpg','png','zip','doc','docx','xls','xlsx'),
                $size=10485760,
                $ordering='name',
                $params=array()
        ){
        $this->files_format=$format;
        $this->base_path= realpath ('../files')."/";
        $resurses=$this->get_list('',$ordering);
        $this->dir=$resurses->dir;
        $this->files=$resurses->files;
        $this->files_size=min($size,$this->parse_size(ini_get('post_max_size')),$this->parse_size(ini_get('upload_max_filesize')));
        
        $this->params=(object)array('max_dir_size'=>0);
        $params=(array)$params;
        if(isset($params['max_dir_size'])){
            $this->params->max_dir_size=intval($params['max_dir_size']);
        }
    }
    function getDirSize($path=''){
        if($path==''){$path=$this->base_path;}
        $files=scandir($path);
        $summ=0;
        if(count($files)){
            foreach($files as $file){
                if(is_file($path.$file)==true){
                    $summ+=filesize($path.$file);
                }
            }
        }
        return $summ;
    }
    function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {        
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }
    function file_upload($file,$newname){
        $res=new jsonClass();
        if($file['name']){
            $tmpName = $file['tmp_name'];
            if(!is_uploaded_file($tmpName)){
                $res->error=jt('CFiles.Err_upload');
            }
            else{
                if(!move_uploaded_file($tmpName, $this->base_path.$newname)){
                    $res->error=jt('CFiles.Err_upload');
                }
                else{
                    $res->result=$newname;
                }
            }
        }
        return $res;
    }
    function file_save($file){
        $res = new jsonClass(null);
        if(strlen($file['name'])){
            if($this->format_check($file['name'])){
                if($file['size']<=$this->files_size){
                    if(($this->params->max_dir_size===0)or(($file['size']+$this->getDirSize())<$this->params->max_dir_size)){
                        $newname=$this->getNewName($file['name']);
                        if (!file_exists($this->base_path.$newname)){
                            $res=$this->file_upload($file,$newname);
                            if($res->error==""){
                                $res->result = $newname;
                            }
                        }
                        else{
                            $res->error=jt('CFiles.file_s_already_exists', $newname);
                        }
                    }
                    else{
                        $res->error=jt('CFiles.dir_too_long_s', $this->size_convert($this->getDirSize()));
                    }
                }
                else{
                    $res->error=jt('CFiles.file_too_long_s', ($this->files_size/1024/1024));
                }
            }
            else{
                $res->error=jt('CFiles.unsupported_format_s_s',$file['name'], $this->get_file_type($file['name']));
            }
        }
        else{
            $res->error=jt('CFiles.insert_file');
        }
        return $res;
    }
    function getNewName($filename){
        $newname=translate($filename);
        if(file_exists($this->base_path.$newname)){
            $extName = $this->get_file_type($newname);
            $name = $this->get_file_name($newname);
            return $name."_".generateGuid().".".$extName;
        }
        return $newname;
    }
    function get_list($filter='',$sort='name'){
        $files=scandir($this->base_path);
        $result=new stdClass();
        $result->files=array();
        $result->dir=array();
        if(count($files)){
            foreach($files as $val){
                if(is_file($this->base_path.$val)==true){
                    if($this->format_check($val)){
                        $result->files[]=$this->file_info($val);
                    }
                }
                else{
                    if(($val!='.')and($val!='..')){
                        $result->dir[]=$val;
                    }
                }
            }            
            if(count($result->files)){
                switch($sort){
                    case 'name_desc';
                        usort($result->files, function($a,$b){
                            return strcasecmp($a['name'],$b['name']);
                        });
                    break;
                }
            }
            if(count($result->dir)){
                switch($sort){
                    case 'name_desc';
                        $result->dir=array_reverse($result->dir);
                    break;
                }
            }
        }
        return $result;
    }
    function file_info($filename){
        $info=stat($this->base_path.$filename);
        $arr=array();
        if($info){
            $arr=array(
                'size'=>$info['size'],
                'changed'=>$info['mtime'],
                'added'=>$info['atime'],
                'size_text'=>$this->size_convert($info['size']),
                'changed_text'=>date('d.m.Y H:i',$info['mtime']),
                'added_text'=>date('d.m.Y H:i',$info['atime']),
                'name'=>$filename,
                'xxx'=>$this->get_file_type($filename)
            );
        }
        return $arr;
    }
    function get_file_type($filename){
        return  trim(mb_strtolower(substr($filename, strrpos($filename, '.') + 1)));
    }
    function get_file_name($filename){
        return  trim(substr($filename,0, strrpos($filename, '.')));
    }
    function file_delete($filename){
        if(file_exists($this->base_path.$filename)){
            if(unlink($this->base_path.$filename)){
                return true;
            }
        }
        return false;
    }
    function file_rename($filename,$newName) {
        if (file_exists($this->base_path . $filename)) {
            $xxx = $this->get_file_type($filename);
            $newName=translate($newName);
            if(mb_strpos($newName,'.')===false){
                $newName.='.'.$xxx;
            }
            else{
                $newName=mb_substr($newName,0,mb_strripos($newName,'.')).'.'.$xxx;
            }
            if ($this->format_check($newName)) {
                if(strlen($newName)){
                    if (!file_exists($this->base_path . $newName)) {
                        if (rename($this->base_path . $filename, $this->base_path . $newName)) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
    function size_convert($bytes){
        $result='';
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "Byte",
                "VALUE" => 1
            ),
        );
        if($bytes==0){
            return '0Byte';
        }
        foreach($arBytes as $arItem){
            if($bytes >= $arItem["VALUE"]){
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))."".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
    function format_check($filename){
        if(count($this->files_format)){
            $xxx= $this->get_file_type($filename);
            if(in_array($xxx,$this->files_format)){
                return true;
            }
        }
        return false;
    }
}
?>