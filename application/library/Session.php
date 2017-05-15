<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 10:36
 */
class Session{

    static $_instance = NULL;

    protected $_session = NULL;

    static function getInstance()
    {
        if (!Session::$_instance) {
            # 用户唯一标识
            empty($_COOKIE['USER_PW']) && @setcookie('USER_PW', $_COOKIE['USER_PW'] = md5(uniqid()), null, '/', null, null, true);
            # 生成 SESSION_ID
            $sessid = md5(md5(isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']: '') . '#郭海龙#' . $_COOKIE['USER_PW']);
            if (empty($_COOKIE['PHPSESSID']) || $_COOKIE['PHPSESSID'] != $sessid) {
                @setcookie('PHPSESSID', $_COOKIE['PHPSESSID'] = $sessid, null, '/', null, null, true);
            }
            session_start();
            Session::$_instance = new Session;
        }
        return Session::$_instance;
    }

    private function __construct()
    {
        $this->_session = $_SESSION;
    }

    public function __get($name)
    {
        if (!isset($this->_session[$name])) {
            $this->_session[$name] = isset($_SESSION[$name])? $_SESSION[$name]: null;
        }
        return $this->_session[$name];
    }

    public function __set($name, $value)
    {
        $this->_session[$name] = $_SESSION[$name] = $value;
    }

    public function __unset($name)
    {
        unset($this->_session[$name], $_SESSION[$name]);
    }

}