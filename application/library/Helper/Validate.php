<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 11:01
 * 数据验证类
 */
namespace Helper;

class Validate{

    /**
     * 验证手机
     */
    static function is_mobile($str){
        if(11 != strlen($str)) return false;
        return preg_match("/13[0-9]{9}|15[0-9]{9}|18[0-9]{9}|147[0-9]{8}|17[0-9]{9}|144[0-9]{8}/", $str);
    }

    /**
     * 验证固定电话
     */
    static function fixed_tel($tel){
        return preg_match("/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$|(^(13[0-9]|15[0|3|6|7|8|9]|18[8|9])\d{8}$)/", $tel);
    }

    /**
     * 验证密码：不能为空+长度
     * @param $key
     * @param $val
     * @param int $min
     * @param int $max
     *
     * @return string 空则正确
     */
    static function is_passwd($key, $val, $min = 0, $max = 0){
        if(empty($_POST[$key])) return $val.'不能为空';
        if($min && strlen($_POST['pw']) < $min) return $val.'不能少于8位';
        if($max && strlen($_POST['pw']) > $max) return $val.'不要超过32位';
        return '';
    }

    /**
     * 验证 email
     */
    static function is_email($str){
        return preg_match("/^[[:alnum:]][a-zA-Z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/", $str);
    }

    /**
     * 验证 日期
     */
    public static function is_date($dt, $min = 0, $max = 0)
    {
        if(date('Y-m-d', strtotime($dt)) != $dt){
            return false;
        }
        if($min && $dt < $min){
            return false;
        }
        if($max && $dt > $max){
            return false;
        }
        return true;
    }

    /**
     * 验证 数值
     */
    static function is_number($str, $min = false, $max = false){
        if(!is_numeric($str)) return false;
        if(false !== $min && $str < $min) return false;
        if(false !== $max && $str > $max) return false;
        return true;
    }

    /**
     * 验证 用户名
     */
    static function name($str){
        return preg_match("/^[\s0-9a-zA-Z\x80-\xff]+$/", $str);
    }

    /**
     * 验证 字母数字
     */
    static function az09($str, $_ = ''){
        return preg_match("/^[0-9a-zA-Z$_]+$/", $str);
    }

    /**
     * 验证 QQ
     */
    static function qq($pStr){
        return preg_match("/^[0-9]{5,12}$/", $pStr);
    }

    /**
     * 验证 危险字符(XSS, 注入)
     */
    static function safe($str){
        return preg_match("/^[\s0-9a-zA-Z\x80-\xff@_?&=.-]+$/", $str);
    }




}