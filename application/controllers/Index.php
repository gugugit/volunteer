<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/10/17
 * Time: 17:37
 */

class IndexController extends Controller_Base {

    /**
     * 首页
    */
    public function indexAction() {//默认Action
        $mActivity = new ActivityModel();
        $datas = $mActivity->fList();
        $this->assign("datas",$datas);
    }

    /**
     * 活动详情
    */
    public function activitydetailAction($id = 0){

        $mActivity = new ActivityModel();
        if(!($id = (int)$id) || !$value = $mActivity->fRow($id)){
            echo "您访问的页面不存在";//这里有问题bug。
        }
        $this->assign("value",$value);
    }
}