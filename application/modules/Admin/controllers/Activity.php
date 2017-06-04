<?php
class ActivityController extends Controller_Admin{

    public $layout = 'admin';

    /**
     * 活动列表
    */
    public function listAction(){
        $this->seo("活动列表");
        $model_activity = new ActivityModel();
        $this->search('id', array('id' => 'ID', 'name' => '活动名称', 'service_hour' => '志愿时长','type' => '1基地2非基地'));
        $datas = $this->_list($model_activity);
        $this->assign("datas",$datas);
    }

    /**
     * 活动添加
    */
    public function addAction(){
        $this->seo("活动添加");
    }

    /**
     * 活动编辑
    */
    public function editAction($id = 0){
        $this->seo("活动编辑");
        $model_activity = new ActivityModel();

        if('POST' == $_SERVER['REQUEST_METHOD']){

            if(empty($id)){

                if(empty($_POST['caption'])){
                    Msg::js('活动标题不能为空');
                }
                if(empty($_POST['start_time'])){
                    Msg::js('请选择活动开始时间');
                }
                if(empty($_POST['end_time'])){
                    Msg::js('请选择活动结束时间');
                }

                if(!isset($_FILES['img'])){
                    Msg::js('请选择上传图片');
                }
                if(empty($_POST['content'])){
                    Msg::js('活动详情不能为空');
                }
                $_POST['created_at'] = date('Y-m-d h:i:s',time());
            }else{
                $_POST['updated_at'] = date('Y-m-d h:i:s',time());
            }
            $_POST['img'] = '/uploag/1.jpg';

            if($id){
                $_POST['id'] = $id;
                unset($_POST['img']);
                $model_activity->update($_POST);
                $upload = $_FILES['img']['tmp_name'];
                if(!empty($upload[0])){
                    $path = APP_PATH . '/public/upload/activity/' .$id.'/';
                    Helper\File::deldir($path);
                    $model_activity->where("id={$id}")->update(['img'=>"/upload/activity/{$id}/0.gif"]);
                    if(Helper\File::upimgs($path,'img'))  Msg::js('更新成功','/admin/activity/list');
                }
                Msg::js('更新成功','/admin/activity/list');

            }else{
                $id = $model_activity->insert($_POST);
                $upload = $_FILES['img']['tmp_name'];
                if(!empty($upload[0])){
                    $path = APP_PATH . '/public/upload/activity/'.$id.'/';
                    $model_activity->where("id={$id}")->update(['img'=>"/upload/activity/{$id}/0.gif"]);
                    if(Helper\File::upimgs($path,'img'))  Msg::js('添加成功','/admin/activity/list');
                }
            }
        }
        # if
        if($id){
            $datas = $model_activity->fRow((int)$id);
            $datas['faceurl'] ="/upload/activity/{$id}/0.gif";
            $this->assign('datas',$datas);
            $this->seo("活动编辑");
        }
    }

    /**
     * 保存更改
     */
    public function saveAction(){
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            foreach ($_POST as $k1 => $v1) {
                if (false === strpos('id,service_hour,limit_num', $k1)) unset($_POST[$k1]);
            }
        }
        if ($this->_save('activity', $_POST)) {
            Msg::ajax('修改成功');
        }

    }
    /**
     * 发布活动
     */
    public function upAction(){

        $model_activity = new ActivityModel();
        if ('POST' == $_SERVER['REQUEST_METHOD']) {

            if(!$model_activity->where("id = {$_POST['id']}")->update(array('status' => '1'))){
                Msg::ajax('发布失败,请联系开发人员');
            }
            Msg::ajax('发布成功',1);
        }
    }
    /**
     * 活动下线
     */
    public function downAction(){

        $model_activity = new ActivityModel();
        if ('POST' == $_SERVER['REQUEST_METHOD']) {

            if(!$model_activity->where("id = {$_POST['id']}")->update(array('status' => '0'))){
                Msg::ajax('下线失败,请联系开发人员');
            }
            Msg::ajax('下线成功',1);
        }
    }

    /**
     * 活动删除
    */
    public function delAction(){

        $model_activity = new ActivityModel();
        if(!$model_activity->where("id = {$_POST['id']}")->del()){
            Msg::ajax('删除失败');
        }
        Msg::ajax('删除成功',1);

    }

    /**
     * 图片上传处理
     */
    public function imgsupAction()
    {
        # 参数
        if (empty($_FILES['upfile'])) Msg::ajax("参数错误");
        \Helper\Validate::safe($_POST['pictitle']) || Msg::ajax("参数错误");
        \Helper\Validate::safe($_POST['fileName']) || Msg::ajax("参数错误");
        # 类型
        $type = array(1 => '.gif', 2 => '.jpg', 3 => '.png');
        # 是否是图片
        if (!$size = getimagesize($_FILES['upfile']['tmp_name'])) Msg::ajax('参数错误');
        if (empty($type[$size[2]])) Msg::ajax('图片类型错误');
        # 保存路径
        $datepath = "upload/activity/" . date("Y-m-d");
        file_exists($datepath) || mkdir($datepath, 0777);
        # 保存图片
        $fileName = $datepath . "/" . rand(1, 10000) . time() . $type[$size[2]];
        if (move_uploaded_file($_FILES["upfile"]["tmp_name"], $fileName)) {
            $state = "SUCCESS";
        } else {
            $state = "c";
        }
        # 返回
        exit("{'url':'" . $fileName . "','title':'" . htmlspecialchars($_POST['pictitle'], ENT_QUOTES) . "','original':'" . htmlspecialchars($_POST['fileName'], ENT_QUOTES) . "','state':'" . $state . "'}");
    }

}