<?php

class ActivityModel extends Db_Mysql{

    public $pk = 'id';

    public $tablename = 'activity';

    #活动类型
    const ACTIVITY_TYPE_BASE_YES = 1;
    const ACTIVITY_TYPE_BASE_NO = 0;

    static $type= array(
        self::ACTIVITY_TYPE_BASE_YES => '基地活动',
        self::ACTIVITY_TYPE_BASE_NO => '非基地活动'
    );

}