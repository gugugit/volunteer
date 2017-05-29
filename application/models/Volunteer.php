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
     * 用户角色
     */
    const ROLE_USER = 0;
    const ROLE_ADMIN = 1;

    static $role = array(
        self::ROLE_USER => '普通用户',
        self::ROLE_ADMIN => '后台用户'
    );


    /**
     * 性别 sex
    */
    const SEX_FEMALE = 0;
    const SEX_MALE = 1;

    static $sex = array(
        self::SEX_FEMALE => '女',
        self::SEX_MALE => '男'
    );

    /**
     * 学院 academy
    */
    const academy_huagong= 1;
    const academy_cailiao = 2;
    const academy_jidian = 3;
    const academy_jingguan = 4;
    const academy_xinxi = 5;
    const academy_lixue = 6;
    const academy_wenfa = 7;
    const academy_shengming = 8;
    const academy_guojiao = 9;
    const academy_nengyuan = 10;
    const academy_bagong = 11;
    const academy_hougong = 12;
    const academy_zhiji = 13;
    const academy_jijiao = 14;

    static $academy = array(
        self::academy_huagong => '化学工程学院',
        self::academy_cailiao => '材料科学与工程学院',
        self::academy_jidian => '机电工程学院',
        self::academy_jingguan => '经济管理学院',
        self::academy_xinxi => '信息科学与技术学院',
        self::academy_lixue => '理学院',
        self::academy_wenfa => '文法学院',
        self::academy_shengming => '生命科学与技术学院',
        self::academy_guojiao => '国际教育学院',
        self::academy_nengyuan => '能源学院',
        self::academy_bagong => '巴黎居里工程师学院',
        self::academy_hougong => '侯德榜工程师学院',
        self::academy_zhiji => '职业技术学院',
        self::academy_jijiao => '继续教育学院'
    );

    /**
     * 盐密
     */
    static function saltpw($pw, $salt)
    {
        return md5(md5($pw) . ', ' . $salt);
    }

    /**
     * 用户提交注册数据
    */
    public function register_data(){
        $register_data = array(
            'username' => $_POST['username'],
            'student_id' => $_POST['studentid'],
            'salt'=>($salt = rand(100000, 999999)),
            'password' => self::saltpw($_POST['password'],$salt),
            'email' => $_POST['email'],
            'mobile' => $_POST['mobile']
        );

        if(!$this->insert($register_data)) {
            return false;
        }
        return true;
    }

}