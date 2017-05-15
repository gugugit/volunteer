<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/11
 * Time: 22:34
 */
class Config{
    /**
     * 通过文件加载配置
     *
     * @param $args [0] 配置路径 + 文件名
     * @param string $args [1] 1级 KEY
     * @param string $args [...]
     * @param string $args [n]N级KEY
     *
     * @return mixed 返回模块内容
     */
    static function ini(){
        static $configs;
        $args = func_get_args();
        if(!isset($configs[$args[0]])){
            $config = new Yaf_Config_Ini(APP_PATH.'/conf/'.$args[0]);
            $configs[$args[0]] = $config->toArray();
        }
        return eval('return $configs["' . join('"]["', $args) . '"];');
    }

    /**
     * DB配置
     * @param mixed $db DB
     * @param mixed $key KEY
     * @return array
     */

    static function db($db = false, $key = false){
        static $configs;
        if(!$configs){
            $file = APP_PATH . "/conf/db.ini";
            $config = new Yaf_Config_Ini($file);
            $configs = $config->toArray();
        }
        if($key) return $configs[$db][$key];
        if($db) return $configs[$db];

        return $configs;

    }

}