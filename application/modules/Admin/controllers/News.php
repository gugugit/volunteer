<?php
class NewsController extends Controller_Admin{

    public $layout='admin';

    /**
     * 新闻列表
    */
    public function listAction(){
        $this->seo("新闻列表");
        $mNew = new NewsModel();
        $this->search('id', array('id' => '新闻ID'));
        $datas = $this->_list($mNew);
        $this->assign("datas",$datas);
    }

    public function addAction(){
        $this->seo("新闻添加");
    }

    /**
     * 新闻编辑
    */
    public function editAction($id = 0){
        $this->seo("新闻编辑");
        $mNews = new NewsModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(empty($id)){
                if(empty($_POST['caption'])){
                    Msg::js('请输入新闻标题');
                }
                if(empty($_POST['content'])){
                    Msg::js("请输入新闻内容");
                }
                if($news_id = $mNews->insert($_POST)){
                    Msg::js('添加成功', '/admin/news/list');
                }else{
                    Msg::js('添加失败,请联系开发人员');
                }
            }
            if($id) {
                $_POST['id'] = $id;
                $mNews->update($_POST);
                Msg::js('更新成功', '/admin/news/list');
            }
        }
        if($id){
            if(!($id = (int)$id) || !$datas = $mNews->fRow($id)){
                Msg::js("sorry～你访问的页面被外星人带走啦");
            }
            $this->assign('datas',$datas);
        }

    }

    /**
     * 新闻发布
    */
    public function upAction(){
        $mNews = new NewsModel();
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            if(!$mNews->where("id = {$_POST['id']}")->update(array('status' => '1'))){
                Msg::ajax('发布失败,请联系开发人员');
            }
            Msg::ajax('发布成功',1);
        }
    }

    /**
     * 新闻下线
    */
    public function downAction(){
        $mNews = new NewsModel();
        if ('POST' == $_SERVER['REQUEST_METHOD']) {

            if(!$mNews->where("id = {$_POST['id']}")->update(array('status' => '0'))){
                Msg::ajax('下线失败,请联系开发人员');
            }
            Msg::ajax('下线成功',1);
        }
    }

    /**
     * 删除新闻
    */
    public function delAction(){
       $mNews = new NewsModel();
        if(!$mNews->where("id = {$_POST['id']}")->del()){
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
        $datepath = "upload/news/" . date("Y-m-d");
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