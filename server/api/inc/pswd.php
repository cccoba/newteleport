<?php defined('_JEXEC') or die('Restricted access');
class pswd
{
    public static function get(string $name = '') : string
    {
        $arr = explode('.', $name);
        if (isset($arr[1])) {
            switch ($arr[0]) {
                case 'bd':
                    $params = array(
                        'host' => 'mysql-5.7',
                        'user' => 'root',
                        'password' => '',
                        'db' => 'newteleport',
                        "prefix" => "g_",
                    );
                    break;
                case "server":
                    $params = array(
                        "tokenName" => "teleportgame_user",
                        "localhostUrl" => "https://newteleport.local/"
                    );
                    break;
                default:
                    $params = array();
            }
            if (isset($params[$arr[1]])) {
                return $params[$arr[1]];
            }
        }

        return '';
    }
}
?>