<?php defined('_JEXEC') or die('Restricted access');
class JConfig
{
    private $config = null;
    private $filename = "";
    private $error = "";
    function __construct($filename = "botConfig.txt")
    {
        $this->filename = $filename;
        $this->error = "";
        $this->config = null;
        $this->load();
    }
    private function load()
    {
        $path = (realpath($this->filename));
        if ($path) {
            $strJsonFileContents = file_get_contents($path);
            $result = json_decode($strJsonFileContents, false);
            $this->config = $result;
            if ($result === null) {
                $this->setError(jt("config.errors.file_not_json", $this->filename));
            }
        } else {
            $this->setError(jt("config.errors.file_not_found", $this->filename));
        }
    }
    public function getError()
    {
        return $this->error;
    }
    public function setError($errorText)
    {
        $this->error = $errorText;
    }
    public function __get($name)
    {
        if ($this->config !== null && isset($this->config->$name)) {
            return $this->config->$name;
        }
        return null;
    }
    public function getData()
    {
        return $this->config;
    }
    public function setData($data)
    {
        $this->config = $data;
        return true;
    }
    public function __set($name, $value)
    {
        if ($this->config !== null) {
            $this->config->$name = $value;
            return true;
        }
        return false;
    }
    public function remove($name)
    {
        if ($this->config !== null) {
            if (isset($this->config->$name)) {
                unset($this->config->$name);
                return true;
            }
        }
        return false;
    }
    public function save()
    {
        if ($this->config !== null) {
            $encodedConfig = json_encode($this->config);
            $path = (realpath($this->filename));
            if ($path) {
                if (file_put_contents($path, $encodedConfig)) {
                    return true;
                } else {
                    $this->setError(jt("config.errors.file_not_writed", $this->filename));
                }
            } else {
                $this->setError(jt("config.errors.file_not_found", $this->filename));
            }
        }
        return false;
    }
}
?>