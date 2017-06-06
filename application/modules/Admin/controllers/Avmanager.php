<?php
class AvmanagerController extends Controller_Admin{

    public $layout='admin';

    /**
     * 列表显示
    */
    public function listAction(){
        $this->seo('参与管理');

        $mAvmanager = new ActivolunteerModel();
        $this->search('activity_id', array('activity_id' => '活动ID','volunteer_id'=>'志愿者ID'));
        $datas = $this->_list($mAvmanager);
        $this->assign('datas',$datas);
    }
    /**
     * 活动参与确认
    */
    public function confirmAction(){
        $manager = new ActivolunteerModel();
        $mvolunteer = new VolunteerModel();

        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(!$manager->where("activity_id={$_POST['activity_id']} and volunteer_id={$_POST['volunteer_id']}")->update(array('join_status' => '1'))){
                Msg::ajax('确认失败,请联系开发人员');
            }
            $avdata= $manager->where("activity_id={$_POST['activity_id']} and volunteer_id={$_POST['volunteer_id']}")->fRow();
            $vdata= $mvolunteer->where("id={$_POST['volunteer_id']}")->fRow();
            $avdata['service_hour'] = $vdata['service_hour'] + $avdata['service_hour'];
            if(!$mvolunteer->where("id={$_POST['volunteer_id']}")->update(array('service_hour' => $avdata['service_hour']))){
                Msg::ajax('失败,请联系开发人员');
            }
            Msg::ajax('确认成功',1);
        }

    }
    /**
     * 活动参与重置
     */
    public function resetAction(){
        $manager = new ActivolunteerModel();
        $mvolunteer = new VolunteerModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(!$manager->where("activity_id={$_POST['activity_id']} and volunteer_id={$_POST['volunteer_id']}")->update(array('join_status' => '0'))){
                Msg::ajax('重置失败,请联系开发人员');
            }
            $avdata= $manager->where("activity_id={$_POST['activity_id']} and volunteer_id={$_POST['volunteer_id']}")->fRow();
            $vdata= $mvolunteer->where("id={$_POST['volunteer_id']}")->fRow();
            $avdata['service_hour'] = $vdata['service_hour'] - $avdata['service_hour'];
            $new_vdata['honesty'] = $vdata['honesty'] + 1;
            if(!$mvolunteer->where("id={$_POST['volunteer_id']}")->update(array('service_hour' => $avdata['service_hour'],'honesty' => $new_vdata['honesty']))){
                Msg::ajax('失败,请联系开发人员');
            }
            Msg::ajax('重置成功',1);
        }

    }
    /**
     * 个人诚信值：默认10分 低于6分将不能报名参与志愿活动
    */
    public function honestyAction(){
        $manager = new ActivolunteerModel();
        $mvolunteer = new VolunteerModel();
        if("POST" == $_SERVER['REQUEST_METHOD']){

            if(!$manager->where("activity_id={$_POST['activity_id']} and volunteer_id={$_POST['volunteer_id']}")->update(array('join_status' => '2'))){
                Msg::ajax('确认未参与失败,请联系开发人员');
            }

            $vdata= $mvolunteer->where("id={$_POST['volunteer_id']}")->fRow();
            $new_vdata['honesty'] = $vdata['honesty'] - 1;
            if(!$mvolunteer->where("id={$_POST['volunteer_id']}")->update(array('honesty' => $new_vdata['honesty']))){
                Msg::ajax('失败,请联系开发人员');
            }
            Msg::ajax('已确认，该志愿者诚信值已减1。',1);
        }
    }

}