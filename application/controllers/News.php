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
        if(!($id = (int)$id) || !$value = $mNews->fRow($id)){
            Msg::js("sorry～你访问的页面被外星人带走啦");
        }
        $this->assign("value",$value);
    }
}