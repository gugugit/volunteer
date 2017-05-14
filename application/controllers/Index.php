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

//        $this->getView()->assign("content", "你好，我是首页");
        $mActivity = new ActivityModel();
        $datas = $mActivity->query('select * from activity');
//        var_dump($datas);
        $this->getView()->assign("datas", $datas);


    }

}