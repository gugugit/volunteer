<?php

/**
 * 后台首页
*/

class IndexController extends Controller_Admin{

    protected $layout = "admin";

    /**
     * 控制面板
    */
    public function indexAction(){
        $this->seo("控制面板");
    }


}