<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/10/17
 * Time: 17:37
 */

class IndexController extends Yaf_Controller_Abstract {

    /**
     * 首页
    */
    public function indexAction() {//默认Action
        $mActivity = new ActivityModel();
        $datas = $mActivity->query('select * from activity');
        $this->getView()->assign("datas", $datas);
    }

    /**
     * 活动详情
    */
    public function activitydetailAction($id = 0){

        $mActivity = new ActivityModel();
        if(!($id = (int)$id) || !$value = $mActivity->fRow($id)){
            echo "您访问的页面不存在";
        }
        $this->getView()->assign("value", $value);

    }
}