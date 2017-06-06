<?php
class VolunteerController extends Controller_Base {

    /**
     * 登录用户个人主页
    */
    public function indexAction()
    {
        $model_volunteer = new VolunteerModel();
        $user_id = $this->user['id'];

        $datas = $this->_list($model_volunteer,"id={$this->user['id']}");

        $this->assign('datas',$datas);

        $service_items = $model_volunteer->query("select a.id,a.caption,a.content,av.created_at,av.updated_at from activity_volunteer av,activity a where av.volunteer_id =$user_id and av.activity_id = a.id;");

        $this->assign('service_items',$service_items);

    }
    /**
     * 志愿者个人详情
    */
    public function volunteerdetailAction($id = 0){
        $mVolunteer = new VolunteerModel();
        if(!($id = (int)$id) || !$v1 = $mVolunteer->fRow($id)){
            Msg::js("sorry～你访问的页面被外星人带走啦");
        }
        $this->assign("v1",$v1);

        $service_items = $mVolunteer->query("select a.id,a.caption,a.content,av.created_at,av.updated_at from activity_volunteer av,activity a where av.volunteer_id =$id and av.activity_id = a.id;");

        $this->assign('service_items',$service_items);
    }

    /**
     * 个人信息编辑
     */
    public function editAction()
    {
        $model_volunteer = new VolunteerModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(!$model_volunteer->where("id = {$this->user['id']}")->update($_POST)){
                Msg::ajax('请完善全部信息再保存！保存之前务必确保信息都正确。');
            }
            Msg::ajax('保存成功',1,'/volunteer/index');
        }
        $datas = $this->_list($model_volunteer,"id={$this->user['id']}");

        $this->assign('datas',$datas);
    }
    /**
     * 修改个人密码
    */
    public function changepwAction(){
        $model_volunteer = new VolunteerModel();

        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(empty($_POST['pw'])){
                Msg::ajax('请输入登录密码');
            }
            if(empty($_POST['newpw'])){
                Msg::ajax('请输入新密码');
            }
            if(empty($_POST['re_newpw'])){
                Msg::ajax('请再次输入新密码');
            }
            if($_POST['newpw'] != $_POST['re_newpw']){
                Msg::ajax('两次输入的新密码不一致，请重新输入');
            }

            #查询当前用户的数据信息
            $volunteer = $model_volunteer->where("id = {$this->user['id']}")->fRow();

            if($volunteer['password'] == md5(md5($_POST['pw']).', '.$volunteer['salt'])){

                $_POST['password'] = VolunteerModel::saltpw($_POST['newpw'],$volunteer['salt']);
                unset($_POST['pw']);
                unset($_POST['newpw']);
                unset($_POST['re_newpw']);

                if(!$model_volunteer->where("id = {$this->user['id']}")->update($_POST)){
                    Msg::ajax('修改失败，请联系管理员');
                }
                Msg::ajax('修改成功');

            }else{
                Msg::ajax('输入登录密码不正确,请重新输入');
            }

        }

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
            if($volunteer['password'] == md5(md5($_POST['password']).', '.$volunteer['salt'])){
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

    /**
     *导出个人志愿信息
    */
    public function exportAction(){
        $mExport = new ExportModel();
        $mVolunteer = new VolunteerModel();
        if("POST" == $_SERVER['REQUEST_METHOD']){
            $id = $_POST['volunteer_id'];
            $result = $mVolunteer->query("select a.id,a.caption,av.created_at,av.updated_at from activity_volunteer av,activity a where av.volunteer_id =$id and av.activity_id = a.id");
            $head_str = "志愿名称,报名时间,参与时间\n";
            $head_str = iconv('utf-8','gb2312',$head_str);
            foreach ($result as $k => $v){
                $caption = iconv('utf-8','gb2312',$v['caption']); //中文转码
                $created_at = iconv('utf-8','gb2312',$v['created_at']);
                $updated_at = iconv('utf-8','gb2312',$v['updated_at']);
                $head_str .= $caption.",".$created_at.",".$updated_at."\n"; //用引文逗号分开
            }
            $filename = date('Y-m-d').'.csv'; //设置文件名
            $mExport->export_csv($filename,$head_str);

        }

    }

}