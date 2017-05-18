<?php
class IntroduceController extends Controller_Admin{

    protected $layout = "admin";

    /**
     * 列表显示
    */
    public function listAction(){

        $model_intro = new IntroduceModel();

        $datas = $this->_list($model_intro);

        $this->assign("datas",$datas);

        $this->seo('总队简介');

    }
    /**
     * 编辑简介
    */
    public function editAction($id = 0){

        $this->seo('总队简介编辑');
        $model_intro = new IntroduceModel();

        if("POST" == $_SERVER['REQUEST_METHOD']){
           $model_intro->update($_POST);
           Msg::js('保存成功','/admin/introduce/list');
       }
        if($id){
            $datas = $model_intro->fRow((int)$id);
            $this->assign('datas',$datas);
        }

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
        $datepath = "upload/intro/" . date("Y-m-d");
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