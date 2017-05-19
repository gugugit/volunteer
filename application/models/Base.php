<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/17
 * Time: 14:58
 */
class BaseModel extends Db_Mysql{

    public $pk = 'id';

    public $tablename = 'base';

    /**
     * 通过id获取图片
     * @param $id
     * @return string
     */
    public static function id2face($id)
    {
        return self::id2facePath($id) . 'img0.png';
    }

    /**
     * 通过id获取图片路径
     * @param $id
     * @return string
     */
    public static function id2facePath($id)
    {
        return '/upload/base/'.$id.'/';
    }
}