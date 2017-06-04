<?php
class BaseController extends Controller_Admin{

    protected $layout="admin";

    /**
     * 基地列表
    */
    public function listAction(){

        $model_base = new BaseModel();

        $this->search('id', array('id' => '基地ID', 'base_name' => '基地名称'));

        $datas = $this->_list($model_base);

        $this->assign('datas',$datas);

        $this->seo("基地列表");
    }

    public function addAction(){
        $this->seo("基地添加");
    }

    /**
     * 基地编辑
    */
    public function editAction($id = 0){
        $model_base = new BaseModel();
        $this->seo("基地编辑");
        if('POST' == $_SERVER['REQUEST_METHOD']){

          if(empty($id)){

              if(empty($_POST['base_name'])){
                  Msg::js('基地名称不能为空');
              }

              if(!isset($_FILES['img'])){
                  Msg::js('请选择上传图片');
              }

              if(empty($_POST['content'])){
                  Msg::js('基地简介不能为空');
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
              $upload = $_FILES['img']['tmp_name'];
              if(!empty($upload[0])){
                  $path = APP_PATH . '/public/upload/base/' .$id.'/';
                  Helper\File::deldir($path);
                  $model_base->where("id={$id}")->update(['img'=>"/upload/base/{$id}/0.gif"]);
                  if(Helper\File::upimgs($path,'img'))  Msg::js('更新成功','/admin/base/list');
              }
              Msg::js('更新成功','/admin/base/list');

          }else{
              $id = $model_base->insert($_POST);

              $upload = $_FILES['img']['tmp_name'];

              if(!empty($upload)){
                  $path = APP_PATH . '/public/upload/base/' . $id . '/';
                  $model_base->where("id={$id}")->update(['img'=>"/upload/base/{$id}/0.gif"]);
                  if(Helper\File::upimgs($path,'img'))  Msg::js('添加成功','/admin/base/list');
              }

              Msg::js('添加成功','/admin/base/list');
          }

        }
        # if
        if($id){
            $datas = $model_base->fRow((int)$id);
            $datas['faceurl'] ="/upload/base/{$id}/0.gif";
            $this->assign('datas',$datas);
            $this->seo("基地编辑");
        }
    }

    /**
     * 基地删除
    */
    public function delAction(){
        $model_base = new BaseModel();
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
        $datepath = "upload/base/" . date("Y-m-d");
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