<?php

class DiscussController extends Controller_Admin{

    public $layout = "admin";

    /**
     * 分享文章列表
    */
    public function listAction(){


        $model_discuss= new DiscussModel();

        $this->search('id', array('id' => 'ID', 'caption' => '标题'));

        $datas = $this->_list($model_discuss);

        $this->assign("datas",$datas);

        $this->seo('文章列表');

    }

    /**
     * 删除分享文章
    */
    public function delAction(){
        $model_discuss = new DiscussModel();
        if(!$model_discuss->where("id = {$_POST['id']}")->del()){
            Msg::ajax('删除失败');
        }
        Msg::ajax('删除成功',1);

    }
}