<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/16
 * Time: 14:53
 */
class ActivolunteerModel extends Db_Mysql{

    public $pk = 'id';

    public $tablename = 'activity_volunteer';

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