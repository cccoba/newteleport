<?php
defined('_JEXEC') or die('Restricted access');
if (!function_exists('_p')) {
    function _p($a, $return = false)
    {
        if ($return) {
            return var_export($a, true);
        }
        echo '<pre>';
        var_export($a);
        echo '</pre>';
    }
    function getGet(string $paramName,$defaultValue = ""){
        if(isset($_GET[$paramName])){
            return $_GET[$paramName];
        }
        return $defaultValue;
    }
    function lg(...$args)
    {
        if (count($args)) {
            foreach ($args as $value) {
                _p($value);
            }
        }
    }
    function cut_text($txt, $limit = 100)
    { //обрезает текст
        if (mb_strlen($txt) > $limit) {
            $txt = mb_substr($txt, 0, $limit) . '...';
        }
        return $txt;
    }

    function parseSearchString($value)
    {
        if (is_array($value)) {
            foreach ($value as $ind => $val) {
                $value[$ind] = parseSearchString($val);
            }
        } else {
            $value = strip_tags($value);
            $value = str_replace('\'', '"', $value);
            $value = htmlspecialchars($value);
        }
        return $value;
    }
    function unparseSearchString($value)
    {
        if (is_array($value)) {
            foreach ($value as $ind => $val) {
                $value[$ind] = unparseSearchString($val);
            }
        } else {
            $value = htmlspecialchars_decode($value);
        }
        return $value;
    }
    function addLog($title, $data, $fileName = "file.txt")
    {
        $start = "-------------$title: " . dateTime(time(), "dateTimeWithSeconds") . "----------------\n";
        $end = "-------------end $title----------------------------------\n";
        $path = realpath(_incPath.DS."..".DS."..".DS."logs");
        file_put_contents($path. DS . $fileName, $start . _p($data, true) . "\n" . $end, FILE_APPEND);
    }
    function dateTime($timestamp, $format = "dateTime", $timeZone = "Asia/Vladivostok")
    {
        switch ($format) {
            case "dateTime":
                $format = "d.m.Y H:i";
                break;
            case "dateTimeWithSeconds":
                $format = "d.m.Y H:i:s";
                break;
            case "date":
                $format = "d.m.Y";
                break;
        }
        $date = new DateTime("now", new DateTimeZone($timeZone));
        $date->setTimestamp($timestamp);
        return $date->format($format);
    }
    function generateGuid($type = 4, $fmt = 101)
    {
        _load('guid');
        return UUID::generate($type, $fmt);
    }
    function generateRandomString($length = 10)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function stringCleaning($text)
    {
        return trim(strtolower($text));
    }
    function redirect($url)
    {
        header("Location: $url");
        exit;
    }
    function getSiteUrl($postfix = "/")
    {
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $postfix;
    }
    function getAuthToken(){
        $token=null;
        if (pswd::get("server.tokenName") && isset($_COOKIE[pswd::get("server.tokenName")])) {
            return $_COOKIE[pswd::get("server.tokenName")];
        }
        elseif (isset($_SERVER['Authorization'])) {
            $token = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $token = trim($requestHeaders['Authorization']);
            }
        }
        return $token;
    }
    function translate($text,$type='text'){
        $text =strtolower(str_replace(array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ъ', 'ы', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь', 'Ъ', 'Ы', 'Э', 'Ю', 'Я'), array('a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'tc', 'ch', 'sh', 'shch', '', '', 'y', 'e', 'iu', 'ia', 'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Zh', 'Z', 'I', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Kh', 'Tc', 'Ch', 'Sh', 'Shch', '', '', 'Y', 'E', 'Iu', 'Ia'), $text));
        switch($type){
            case 'username':
                $text = strtolower(preg_replace( '#\.\.|[^a-z\_.]#', '', $text ));
            break;
            case 'name':
                $text = strtolower(preg_replace( '#\.\.|[^a-z\-]#', '', $text ));
            break;
            case 'fname':
                $text = strtolower(preg_replace( '#[^a-z]#', '', $text ));
            break;
            default:
               $text =preg_replace(array('/\.+/','/ +/','/[^\w.-]/i'),array('.',' ',''),$text);
               $text = str_replace( ' ', '_', $text );
        }
        
        return $text;
    }

}

?>