<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/11/4
 * Time: 17:06
 */
class VolunteerModel extends Db_Mysql {

    #主键
    public $pk = 'id';

    #数据表
    public $tablename = 'volunteer';

    #用户提交注册数据
    public function register_data(){
        $register_data = array(
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'email' => $_POST['email'],
            'mobile' => $_POST['mobile'],
            'true_name' => $_POST['username']
        );
        if(!$this->insert($register_data)) {
            return false;
        }
        return true;
    }

}