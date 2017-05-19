<?php
class MemberModel extends Db_Mysql{

    public $pk = 'id';

    public $tablename = 'member';

    #组别
    #外联组
    const GROUP_ID_EXTERNAL_CON = 1;
    #资料评定组
    const GROUP_ID_DATA_JUDGE = 2;
    #培训组
    const GROUP_ID_CULTIVATE = 3;
    #新媒体组
    const GROUP_ID_NEW_MEDIA = 4;
    #记者组
    const GROUP_ID_REPORTER  = 5;
    #形象推广组
    const GROUP_ID_VIVID = 6;
    #节能环保组
    const GROUP_ID_GREEN = 7;

    #标志
    static $group_id= array(
        self::GROUP_ID_EXTERNAL_CON => '外联组',
        self::GROUP_ID_DATA_JUDGE =>'资料评定组',
        self::GROUP_ID_CULTIVATE =>'培训组',
        self::GROUP_ID_NEW_MEDIA =>'新媒体组',
        self::GROUP_ID_REPORTER =>'记者组',
        self::GROUP_ID_VIVID =>'形象推广组',
        self::GROUP_ID_GREEN =>'节能环保组'
    );


}