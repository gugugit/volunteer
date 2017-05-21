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
           $model_activity = new ActivityModel();

           if($model_activolunteer->activity_register()){
               $already_num_array = $model_activity->query("select already_num from activity where id={$_POST['activityid']} ");

               $already_num_array['0']['already_num'] = $already_num_array['0']['already_num'] + 1 ;

               $model_activity->where("id={$_POST['activityid']}")->update(array('already_num' => $already_num_array['0']['already_num']));

               Msg::ajax("报名成功！");
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