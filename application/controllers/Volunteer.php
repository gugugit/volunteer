<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/11/4
 * Time: 10:41
 */
class VolunteerController extends Yaf_Controller_Abstract{
    /**
     * 用户首页
    */
    public function indexAction()
    {

    }

    /**
     *
     * 登录
     */
    public function loginAction(){

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
                Msg::ajax("注册成功！");
            }

        }
//        Msg::js('注册失败，请稍后再试！');
    }

}