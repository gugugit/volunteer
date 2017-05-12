<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/12
 * Time: 23:14
 * Redis 工厂类
 */
class Db_Redis{

    /**
     * Redis 对象数组
     */
    private static $obj;

    /**
     * 实例化对象（单例）
     * @param string $config
     * @return mixed
     */
    static function instance($config = 'default')
    {
        if (!isset(self::$obj[$config])) {
            # 配置
            if (!$redisconfig = Yaf_Registry::get("config")->redis->$config) {
                exit('redis config error: ' . $config);
            }
            # 连接
            self::$obj[$config] = new Redis();
            if (self::$obj[$config]->pconnect($redisconfig->host, isset($redisconfig->port)? $redisconfig->port: 6379)) {
                self::$obj[$config]->select($redisconfig->db);
            }
        }
        return self::$obj[$config];
    }

    /**
     * 魔术方法，实现所有REDIS自有方法
     *
     * @param string $method REDIS函数方法(如果方法名包含 _json 后缀，则返回 json 格式)
     * @param array $args
     * @return mixed
     */
    static function __callstatic($method, $args)
    {
        # 初始化数据
        $cmd = $result = array();
        if ($pos = strpos($method, '_json')) {
            $method = substr($method, 0, $pos);
        }
        # 处理参数
        foreach ($args as $k => $arg) {
            is_array($arg) && ($args[$k] = json_encode($arg));
            $cmd[] = '$args['.$k.']';
        }
        # 执行REDIS查询
        eval('$result = Db_Redis::instance()->' . $method . '(' . join(',', $cmd) . ');');
        # 返回JSON格式
        if ($pos) {
            if(!$json = json_decode($result, true)){
                $json = array();
            }
            return $json;
        }
        # 返回字符串格式
        return $result;
    }

}