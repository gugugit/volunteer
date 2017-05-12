<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/11/4
 * Time: 10:19
 */
/*命令空间是必须要加上的,在全局使用我们自己的类库的时候*/
class Tool_Http{
    public static function getHost(){
        return $_SERVER['HTTP_HOST'];
    }
}