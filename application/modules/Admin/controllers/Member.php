<?php
class MemberController extends Controller_Admin{

    protected $layout="admin";


    /**
     * 会员列表
    */
    public function listAction(){

        $this->seo("会员列表");

        $model_member = new MemberModel();

        $this->search('id', array('id' => 'ID', 'name' => '姓名', 'group_id' => '组ID:1234567'));
        $datas = $this->_list($model_member);

        $this->assign("datas",$datas);
    }

    /**
     * 会员添加
    */
    public function addAction(){
        $this->seo("会员添加");

    }

    /**
     * 会员编辑
     */
    public function editAction($id = 0){

        $this->seo("会员编辑");
        $model_base = new MemberModel();

        if('POST' == $_SERVER['REQUEST_METHOD']){

            if(empty($id)){

                if(empty($_POST['name'])){
                    Msg::js('会员姓名不能为空');
                }

                if(!isset($_FILES['img'])){
                    Msg::js('请选择上传图片');
                }

                if(empty($_POST['content'])){
                    Msg::js('个人简介不能为空');
                }
                $_POST['created_at'] = date('Y-m-d h:i:s',time());
            }else{
                $_POST['updated_at'] = date('Y-m-d h:i:s',time());
            }
            $_POST['img'] = '/uploag/1.jpg';

            if($id){
                $_POST['id'] = $id;
                unset($_POST['img']);
                $model_base->update($_POST);
                Msg::js('更新成功','/admin/member/list');
            }else{
                $id = $model_base->insert($_POST);
                $upload = $_FILES['img']['tmp_name'];

                if(!empty($upload)){
                    $path = APP_PATH . '/public/upload/member/' . $id . '/';
                    $model_base->where("id={$id}")->update(['img'=>"/upload/member/{$id}/0.gif"]);
                    if(Helper\File::upimgs($path,'img'))  Msg::js('添加成功','/admin/member/list');
                }

            }

        }
        # if
        if($id){
            $datas = $model_base->fRow((int)$id);
            $datas['faceurl'] ="/upload/member/{$id}/0.gif";
            $this->assign('datas',$datas);
            $this->seo("会员编辑");
        }

    }

    /**
     * 保存更改
    */
    public function saveAction(){
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            foreach ($_POST as $k1 => $v1) {
                if (false === strpos('id,group_id,sort_id', $k1)) unset($_POST[$k1]);
            }
        }
        if ($this->_save('member', $_POST)) {
            Msg::ajax('修改成功');
        }

    }
    /**
     * 会员删除
     */
    public function delAction(){

        $model_base = new MemberModel();
        if(!$model_base->where("id = {$_POST['id']}")->del()){
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
        $datepath = "upload/member/" . date("Y-m-d");
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