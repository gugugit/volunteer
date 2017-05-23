<?php
class VolunteerController extends Controller_Base {

    /**
     * 个人主页
    */
    public function indexAction()
    {
        $model_volunteer = new VolunteerModel();

        $datas = $this->_list($model_volunteer,"id={$this->user['id']}");

        $this->assign('datas',$datas);
    }

    /**
     * 个人信息编辑
     */
    public function editAction()
    {
        $model_volunteer = new VolunteerModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){

            if(!$model_volunteer->where("id = {$this->user['id']}")->update($_POST)){
                Msg::ajax('保存失败,请联系开发人员');
            }
            Msg::ajax('保存成功',1,'/volunteer/index');
        }

        $datas = $this->_list($model_volunteer,"id={$this->user['id']}");

        $this->assign('datas',$datas);

    }

    /**
     *
     * 登录
     */
    public function loginAction(){
        if("POST" == $_SERVER['REQUEST_METHOD']){
            if(empty($_POST['studentid'])||empty($_POST['password'])){
                Msg::ajax(" 学号或密码为空，请输入");
            }
            if(!\Helper\Validate::is_number($_POST['studentid'])){
                Msg::ajax("学号格式错误，请重新输入");
            }

            $model_volunteer = new VolunteerModel();
            if(!$volunteer = $model_volunteer->where("student_id='{$_POST['studentid']}'")->fRow()){
                Msg::ajax("该学号没有注册呢，请先注册一下");
            }

            if($volunteer['password'] == $_POST['password']){
                $this->session->user = $volunteer;
                Msg::ajax('',1,'/volunteer/index');
            }else{
                Msg::ajax('密码不正确或者学号输入错误',1,'/volunteer/login');
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
            if(empty($_POST['studentid'])){
                Msg::ajax("学号为空，请输入");
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
                Msg::ajax("注册成功！赶快去登录完善一下个人信息哦～以便于我们与你取得联系。祝生活愉快！",1,"/volunteer/login");
            }

        }
    }

}