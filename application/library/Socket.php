<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 18:04
 */
class Socket
{
    /**
     * 服务端
     *
     * @param string $ip 服务器IP
     * @param int $port 端口号
     * @param object $cls 回调类对象
     * @param string $fnc 回调类函数名
     */
    static function server($ip, $port, &$cls, $fnc)
    {
        if (!$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) {
            exit("socket_create() 失败:" . socket_strerror($sock) . "\n");
        }
        if (!$ret = socket_bind($sock, $ip, $port)) {
            exit("socket_bind() 失败:" . socket_strerror($ret) . "\n");
        }
        if (!$ret = socket_listen($sock, 4)) {
            exit("socket_listen() 失败:" . socket_strerror($ret) . "\n");
        }
        while(true){
            if (!$msgsock = socket_accept($sock)) {
                echo "socket_accept() 失败: " . socket_strerror($msgsock) . "\n";
                break;
            }
            # 接收信息
            if(!$read = socket_read($msgsock, 8192)){
                echo "socket_read() 失败: " . socket_strerror($read) . "\n";
                break;
            }
            $msg = $cls->$fnc($read);
            # 返回给客户端
            socket_write($msgsock, $msg, strlen($msg));
            socket_close($msgsock);
        }
        socket_close($sock);
    }

    /**
     * 客户端
     *
     * @param string $ip 服务器IP
     * @param int $port 端口号
     * @param array||json $json 提交数据
     *
     * @return mixed
     */
    static function client($ip, $port, $json)
    {
        if (!$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) {
            Msg::ajax('系统错误，请重试[SC-1]');
        }
        if (!$result = @socket_connect($socket, $ip, $port)) {
            Msg::ajax('系统错误，请重试[SC-2]');
        }
        if(is_array($json)){
            $json = json_encode($json);
        }
        if (!socket_write($socket, $json, strlen($json))) {
            Msg::ajax('系统错误，请重试[SC-3]');
        }
        $out = socket_read($socket, 8192);
        socket_close($socket);
        return json_decode($out, true);
    }
}