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
        $this->getView()->assign("content", "你好，我是首页");
        $mUser = new UserModel();
        $datas = $mUser->query('select * from user');
        var_dump($datas);

    }



}