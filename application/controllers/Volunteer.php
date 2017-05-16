<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/11/4
 * Time: 10:41
 */
class VolunteerController extends Controller_Base {
    /**
     * 个人主页
    */
    public function indexAction()
    {

    }

    /**
     *
     * 登录
     */
    public function loginAction(){
        if("POST" == $_SERVER['REQUEST_METHOD']){
            if(empty($_POST['mobile'])||empty($_POST['password'])){
                Msg::ajax(" 电话或密码为空，请输入");
            }
            if(!\Helper\Validate::is_mobile($_POST['mobile'])){
                Msg::ajax("电话格式错误，请重新输入");
            }

            $model_volunteer = new VolunteerModel();
            if(!$volunteer = $model_volunteer->where("mobile='{$_POST['mobile']}'")->fRow()){
                Msg::ajax("该电话没有注册，请先注册");
            }

            if($volunteer['password'] == $_POST['password']){
                $this->session->user = $volunteer;
                Msg::ajax('',1,'/index');
            }
        }

    }

    /**
     * 退出登出
    */
    public function logoutAction(){
        if(isset($this->user)){
            unset($this->user,$this->session->user);
            @setcookie('USER', null, $expire = time() - 3600, '/');
        }
        Msg::js("",'/index');
    }

    /**
     * 注册
     */
    public function registerAction(){

        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(!\Helper\Validate::name($_POST['username']) || empty($_POST['username'])){
                Msg::ajax("姓名为空或者输入姓名不合法");
            }
            if(!\Helper\Validate::is_mobile($_POST['mobile']) || empty($_POST['mobile'])){
                Msg::ajax("输入手机号码为空或者不合法");
            }
            if(!\Helper\Validate::is_email($_POST['email']) || empty($_POST['email'])){
                Msg::ajax("输入邮箱为空或者不合法");
            }
            if(empty($_POST['password'])){
                Msg::ajax("输入密码为空，请输入");
            }
            if(empty($_POST['re_password'])){
                Msg::ajax("输入重复密码为空，请输入");
            }
            if($_POST['password'] != $_POST['re_password']){
                Msg::ajax("密码和重复密码不一致，请重新输入");
            }
            $model_volunteer = new VolunteerModel();
            if($model_volunteer->register_data()){
                Msg::ajax("注册成功！",1,"/volunteer/login");
            }

        }
    }

}