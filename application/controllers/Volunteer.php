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

        $model_volunteer = new VolunteerModel();

        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(empty($_POST['username']) || \Helper\Validate::name($_POST['username'])){
                Msg::ajax("请输入姓名");
            }

        }


    }

}