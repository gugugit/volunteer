<?php

class VolunteerModel extends Db_Mysql {

    #主键
    public $pk = 'id';

    #数据表
    public $tablename = 'volunteer';

    #邮箱认证状态
    #否
    const EMAIL_VALID_NO = 0;
    #是
    const EMAIL_VALID_YES = 1;

    #标志
    static $email_valid = array(
        self::EMAIL_VALID_NO => '否',
        self::EMAIL_VALID_YES => '是'
    );

    /**
     * 角色
     */
    const ROLE_USER = 0;
    const ROLE_ADMIN = 1;

    static $role = array(
        self::ROLE_USER => '普通用户',
        self::ROLE_ADMIN => '后台用户'
    );

    # 性别
    const SEX_FEMALE = 0;
    const SEX_MALE = 1;

    static $sex = array(
        self::SEX_FEMALE => '女',
        self::SEX_MALE => '男'
    );



    #用户提交注册数据
    public function register_data(){
        $register_data = array(
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'email' => $_POST['email'],
            'mobile' => $_POST['mobile']
        );

        if(!$this->insert($register_data)) {
            return false;
        }
        return true;
    }

}