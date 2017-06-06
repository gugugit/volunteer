<?php

class IndexController extends Controller_Base {

    /**
     * 首页
    */
    public function indexAction() {//默认Action
        $mActivity = new ActivityModel();

        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(empty($_POST['soso'])||!\Helper\Validate::safe($_POST['soso'])){
                Msg::js('请输入搜索的内容');
            }
            $datas = $this->_list($mActivity,"status=1&L=16&caption=LIKE *{$_POST['soso']}*");
            if(empty($datas)){
                Msg::js('没有您要查询的内容,请重新输入',0);
            }
            $this->assign("datas",$datas);
        }else{
            $datas = $this->_list($mActivity,"status=1&L=16");
            $this->assign("datas",$datas);
        }
    }

    /**
     * 活动详情
    */
    public function activitydetailAction($id = 0){
       if("POST" == $_SERVER['REQUEST_METHOD']){
           $model_activolunteer = new ActivolunteerModel();
           $model_activity = new ActivityModel();
           $mActivity = new ActivityModel();
           $model_volunteer = new VolunteerModel();

//           $volun_info = $model_volunteer->where("id={$_POST['volunteer_id']}")->fRow();
//           if($volun_info['honesty'] < 6){
//               Msg::ajax("您的诚信值低于6分，暂时不能报名参与志愿活动! 请及时联系我们，恢复诚信值！");
//           }
//
//           $data = $model_activolunteer->field('activity_id')->table('activity_volunteer')->where("volunteer_id={$_POST['volunteer_id']}")->fList();
//
//           foreach ($data as $value){
//               if($value['activity_id'] == $_POST['activity_id']){
//                   Msg::ajax("你已经报名了该活动，请勿重复报名");
//               }
//           }
          if($_POST['volunteer_id']){

              $volun_info = $model_volunteer->where("id={$_POST['volunteer_id']}")->fRow();
              if($volun_info['honesty'] < 6){
                  Msg::ajax("您的诚信值低于6分，暂时不能报名参与志愿活动! 请及时联系我们，恢复诚信值！");
              }

              $data = $model_activolunteer->field('activity_id')->table('activity_volunteer')->where("volunteer_id={$_POST['volunteer_id']}")->fList();

              foreach ($data as $value){
                  if($value['activity_id'] == $_POST['activity_id']){
                      Msg::ajax("你已经报名了该活动，请勿重复报名");
                  }
              }

              $data= $mActivity->where("id={$_POST['activity_id']}")->fRow();
              $_POST['service_hour'] = $data['service_hour'];
              $model_activolunteer->insert($_POST);
              $already_num_array = $model_activity->query("select already_num from activity where id={$_POST['activity_id']} ");

              $already_num_array['0']['already_num'] = $already_num_array['0']['already_num'] + 1 ;

              $model_activity->where("id={$_POST['activity_id']}")->update(array('already_num' => $already_num_array['0']['already_num']));

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