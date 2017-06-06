<?php
class ActivolunteerModel extends Db_Mysql{

    public $pk = 'activity_id';

    public $tablename = 'activity_volunteer';

    #参与状态
    const JOIN_STATUS_NO = 2;
    const JOIN_STATUS_YES = 1;
    const JOIN_STATUS_WAIT = 3;

    static $join_status = array(
        self::JOIN_STATUS_NO => '未参与',
        self::JOIN_STATUS_YES => '已参与',
        self::JOIN_STATUS_WAIT => '待参与'
    );


    #用户报名活动数据
    public function activity_register(){
        $activity_register = array(
            'activity_id' => $_POST['activityid'],
            'volunteer_id' => $_POST['volunteerid'],
        );

        if(!$this->insert($activity_register)) {
            return false;
        }
        return true;
    }

}