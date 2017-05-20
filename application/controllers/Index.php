<?php

class IndexController extends Controller_Base {

    /**
     * 首页
    */
    public function indexAction() {//默认Action
        $mActivity = new ActivityModel();
        $datas = $this->_list($mActivity,"status=1&L=16");
        $this->assign("datas",$datas);
    }

    /**
     * 活动详情
    */
    public function activitydetailAction($id = 0){

       if("POST" == $_SERVER['REQUEST_METHOD']){

           $model_activolunteer = new ActivolunteerModel();

           if($model_activolunteer->activity_register()){
               Msg::ajax("报名成功！",1,"/index");
           }else{
               Msg::ajax("热爱志愿的你，要先登录噢～",1,"/volunteer/login");
           }

       }else{
           $mActivity = new ActivityModel();
           if(!($id = (int)$id) || !$value = $mActivity->fRow($id)){
               echo "您访问的页面不存在";//这里有问题bug。
           }
           $this->assign("value",$value);
       }

    }
}