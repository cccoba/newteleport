<?php defined('_JEXEC') or die('Restricted access');

class CobaMySQLi
{
    public $connects = array();
    public $errors = array();
    private $link;
    function __construct($connects = NULL)
    {
        if ($connects == NULL) {
            $connects = new stdClass();
        }
        if (!isset($connects->host)) {
            $connects->host = pswd::get('bd.host');
        }
        if (!isset($connects->user)) {
            $connects->user = pswd::get('bd.user');
        }
        if (!isset($connects->password)) {
            $connects->password = pswd::get('bd.password');
        }
        if (!isset($connects->db)) {
            $connects->db = pswd::get('bd.db');
        }
        $this->connects = $connects;
        $this->errors = array();

        $this->link = new mysqli($this->connects->host, $this->connects->user, $this->connects->password);
        if ($this->link->connect_errno) {
            $this->setErrors($this->link->connect_error);
        } else {
            if (!$this->link->select_db($this->connects->db)) {
                $this->setErrors($this->link->error);
            }
        }
    }
    function __destruct()
    {
        $this->link->close();
    }
    function setErrors($error)
    {
        if (is_array($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors[] = $error;
        }
    }
    public function getErrors()
    {
        return $this->errors;
    }
    public function query($sql, $type = 'list', $limitstart = 0, $limit = 0, $id = NULL)
    {
        $limit = intval($limit);
        $limitstart = intval($limitstart);
        if ($limit or $limitstart) {
            $sql .= ' LIMIT ' . $limitstart . ', ' . $limit;
        }
        $res = (object) array('result' => false, 'errors' => array());
        if (!count($this->errors)) {
            $result = $this->link->query($sql);
            if (!$result) {
                $res->errors[] = $this->link->error;
            } else {
                switch ($type) {
                    case 'array': //array(0=>'aaa',1=>'bbb')
                        $res->result = array();
                        while ($row = $result->fetch_array(MYSQL_NUM)) {
                            $res->result[] = $row[0];
                        }
                        break;
                    case 'row'; //object({a:'aaa',b:'bbb'})
                        $row = $result->fetch_object();
                        if ($row) {
                            $res->result = $row;
                        }
                        break;
                    case 'result'; //'aaa'
                        $row = $result->fetch_row();
                        if ($row and isset($row[0])) {
                            $res->result = $row[0];
                        }
                        break;
                    case 'edit':
                        if ($result) {
                            $res->result = true;
                        }
                        return $res;
                        break;
                    case 'insert':
                        if ($result) {
                            if ($this->link->insert_id) {
                                $res->result = $this->link->insert_id;
                            } else {
                                $res->result = true;
                            }
                        }
                        return $res;
                        break;
                    default:
                        $res->result = array();
                        while ($row = $result->fetch_object()) {
                            if (is_null($id)) {
                                $res->result[] = $row;
                            } else {
                                $res->result[$row->$id] = $row;
                            }
                        }
                }
            }
        } else {
            $res->errors = $this->getErrors();
        }
        return $res;
    }
    public function escapeString($text)
    {
        return $this->link->real_escape_string($text);
    }
    public function beginTransaction()
    {
        $this->link->begin_transaction();
    }
    public function commitTransaction()
    {
        $this->link->commit();
    }
    public function rollbackTransaction()
    {
        $this->link->rollback();
    }

}
?>