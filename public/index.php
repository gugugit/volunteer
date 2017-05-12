<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/10/17
 * Time: 17:35
 */

//设置编码格式
//header("Content-type:text/html;charset=utf-8");
//define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
////echo APP_PATH;
//$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
//
////当bootstrap被调用的时刻, Yaf_Application就会默认的在APPLICATION_PATH下, 寻找Bootstrap.php,
////而这个文件中, 必须定义一个Bootstrap类, 而这个类也必须继承自Yaf_Bootstrap_Abstract.
//
//$app->bootstrap()->run();

foreach (array("'", '%27', '"', '%22', '(', '%28', '*', '%2A', '.php', '.js', '.css', '.png', '.jpg', '.gif', '.txt', '.ico', '.ini', '.ssh', '.svn', '.log','.rar','.zip','.asp','editor','.sql') as $k1 => $v1) if(false !== strpos($_SERVER['REQUEST_URI'], $v1)) {
    header("HTTP/1.1 204"); exit;
}

# 用户IP
foreach(array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR') as $v1) if(!empty($_SERVER[$v1])) {
    ($tPos = strpos($ip = $_SERVER[$v1], ',')) && $ip = substr($ip, 0, $tPos); break;
}
define('USER_IP', long2ip(ip2long($ip)));
# 全局
define("APP_PATH", realpath((phpversion() >= "5.3"? __DIR__: dirname(__FILE__)) . '/../'));
date_default_timezone_set("Asia/Shanghai");

$app = new Yaf_Application(APP_PATH . "/conf/application.ini", 'common');
$app->bootstrap()->run();