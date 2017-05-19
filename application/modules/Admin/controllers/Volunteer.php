<?php
class VolunteerController extends Controller_Admin{

    public $layout="admin";

    /**
     * 志愿者信息列表
    */
    public function listAction(){

        $this->seo("志愿者列表");
        $model_volunteer = new VolunteerModel();

        $this->search('id', array('id' => '志愿者ID', 'username' => '姓名','mobile' => '电话'));
        $datas = $this->_list($model_volunteer);
        $this->assign("datas",$datas);
    }


    /**
     * 志愿者添加
    */
    public function addAction(){
        $this->seo('添加志愿者');

    }

     /**
      * 志愿者编辑
     */
     public function editAction($id = 0){

         $this->seo("志愿者编辑");
         $model_volunteer = new VolunteerModel();

         if('POST' == $_SERVER['REQUEST_METHOD']){

             if(empty($id)){
                 if(empty($_POST['username'])|| !Helper\Validate::name($_POST['username'])){
                     Msg::js("姓名为空或者输入格式错误");
                 }
                 if(empty($_POST['password'])){
                     Msg::js("密码不能为空");
                 }
                 if(empty($_POST['student_id'])){
                     Msg::js("学号不能为空");
                 }
                 if(empty($_POST['class'])|| !Helper\Validate::name($_POST['class'])){
                     Msg::js("班级不能为空或者输入格式错误");
                 }
                 if(empty($_POST['academy_id'])){
                     Msg::js("必须选择所属学院");
                 }
             }

             if($id){
                 $_POST['id'] = $id;
                 $model_volunteer->update($_POST);
                 Msg::js('更新成功','/admin/volunteer/list');

             }else{
                 $model_volunteer->insert($_POST);
                 Msg::js('添加成功','/admin/volunteer/list');
             }
         }

         # if-end
         if($id){
             $datas = $model_volunteer->fRow((int)$id);
             $this->assign('datas',$datas);
             $this->seo("志愿者编辑");
         }

     }

    /**
     * 志愿者删除
    */
    public function delAction(){
        $model_base = new VolunteerModel();
        if(!$model_base->where("id = {$_POST['id']}")->del()){
            Msg::ajax('删除失败');
        }
        Msg::ajax('删除成功',1);

    }

    /**
     * 志愿者详情
    */
    public function detailAction($id = 0){

        $this->seo("用户详情");
        $model_volunteer = new VolunteerModel();

        if (!($id = (int)$id) || !$datas = $model_volunteer->fRow($id)) {
            Msg::js('用户不存在');
        }
        $this->assign('datas',$datas);
    }

    /**
     * 志愿字段的修改
    */
    public function saveAction(){

        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            foreach ($_POST as $k1 => $v1) {
                if (false === strpos('id,role,academy_id', $k1)) unset($_POST[$k1]);
            }
        }
        if ($this->_save('volunteer', $_POST)) {
            Msg::ajax('修改成功');
        }
    }



}