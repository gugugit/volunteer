<?php
class NewsController extends Controller_Base{

    /**
     * 新闻列表
    */
    public function listAction(){
        $mNews = new NewsModel();
        $datas = $this->_list($mNews,'L=8&status=1');
        $this->assign('datas',$datas);
    }

    /**
     * 新闻详情
    */
    public function detailAction($id = 0){
        $mNews = new NewsModel();
        if(($id = (int)$id)){
            $page_view = $mNews->fRow($id);
            $page_view['page_view'] = $page_view['page_view'] + 1;
            $mNews->where("id={$id}")->update(array("page_view" => $page_view['page_view']));
            $value = $mNews->fRow($id);
        }else{
            Msg::ajax('sorry～你访问的页面被外星人带走啦',1,'/news/list');
        }
        $this->assign("value",$value);
    }
}