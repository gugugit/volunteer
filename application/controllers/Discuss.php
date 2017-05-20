<?php
class DiscussController extends Controller_Base{

    /**
     * 我要分享
    */
    public function shareAction(){
        $model_discuss = new DiscussModel();

      if('POST' == $_SERVER['REQUEST_METHOD']){
          if(empty($_POST['caption'])){
              Msg::ajax("请输入标题");
          }
          if(empty($_POST['content'])){
              Msg::ajax("请输入分享内容");
          }
          if($model_discuss->share_data()){
              Msg::ajax("发布成功！",1,"/discuss/list");
          }else{
              Msg::ajax("热爱志愿的你，要先登录噢～",1,"/volunteer/login");
          }
      }

    }


    /**
     * 分享列表
    */
    public function listAction(){

        $model_discuss = new DiscussModel();

        $datas = $model_discuss->query('select s.id,v.username,s.content,s.caption,s.created_at from share s left join volunteer v on s.volunteer_id=v.id order by s.created_at desc');

        $this->assign('datas',$datas);
    }

    /**
     * 详情页
    */
    public function detailAction($id = 0){

        $model_discuss = new DiscussModel();
        if($id){
            $datas = $model_discuss->query("select s.id,s.tags,v.username,s.content,s.caption,s.created_at from volunteer v,share s where s.volunteer_id=v.id and s.id={$id}");
            $this->assign('datas',$datas);
        }else{
            Msg::ajax('sorry～你访问的页面被外星人带走啦',1,'/discuss/list');
        }

    }

    /**
     * 点赞
    */
    public function tagsAction(){

//        if ('POST' == $_SERVER['REQUEST_METHOD']) {
//            Msg::ajax("谢谢");
//
//        }

//        if ($this->_save('share', $_POST)) {
//            Msg::ajax('修改成功');
//        }

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
        $datepath = "upload/share" . date("Y-m-d");
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