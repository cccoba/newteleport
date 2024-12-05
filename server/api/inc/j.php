<?php defined('_JEXEC') or die('Restricted access');
class JText
{
    private $iniData = array();
    function __construct()
    {
        $path = (realpath('language' . DS . 'ru-RU.ini'));
        if ($path) {
            $iniData = parse_ini_file($path);
            if ($iniData !== false) {
                $this->iniData = $iniData;
                return;
            }
        }
        $this->iniData = array();
    }
    public function _($text = '')
    {
        if (strlen($text)) {
            if (count($this->iniData)) {
                if (isset($this->iniData[strtoupper($text)])) {
                    $value = $this->iniData[strtoupper($text)];
                    $value = $this->replaceTextVariables($value);
                    return $this->replaceBr($value);
                }
            }
            return $text;
        }
        return '';
    }
    private function replaceBr($text)
    {
        return str_replace("[br]", "\n", $text);
    }
    public function replaceTextVariables($text)
    {
        if (preg_match_all('/###[A-Z._\d]+###/', $text, $found)) {
            foreach ($found[0] as $value) {
                $indexName = mb_substr($value, 3, -3);
                $replaceValue = $this->_($indexName);
                $text = str_replace($value, $replaceValue, $text);
            }
        }
        return $text;
    }
    public function sprintf($text = '', ...$args)
    {
        $value = $this->_($text);
        return sprintf($value, ...$args);
    }

}

function jt($value, ...$args)
{
    $jtObj = new JText();
    if (count($args)) {
        return $jtObj->sprintf($value, ...$args);
    }
    return $jtObj->_($value);
}
function jt_tl($value, ...$args)
{
    $value = jt($value, ...$args);
    return trim(strtolower($value));
}

function jt_arr($arr, $separator = "\n")
{
    if (count($arr)) {
        $jtObj = new JText();
        $results = [];
        foreach ($arr as $value) {
            $results[] = $jtObj->_($value);
        }
        return implode($results, [$separator]);
    }
    return "";
}
?>