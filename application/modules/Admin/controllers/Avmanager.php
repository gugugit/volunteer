<?php
class AvmanagerController extends Controller_Admin{

    public $layout='admin';

    /**
     * 列表显示
    */
    public function listAction(){
        $this->seo('参与管理');
        $mExport = new ExportModel();
        $mAvmanager = new ActivolunteerModel();
        $mVolunteer = new VolunteerModel();
        $mActivity = new ActivityModel();
        $this->search('activity_id', array('activity_id' => '活动ID','volunteer_id'=>'志愿者ID','join_status'=>'参与状态1|2|3'));
        $datas = $this->_list($mAvmanager,'L=20');
        $this->assign('datas',$datas);

        if("POST" == $_SERVER['REQUEST_METHOD']){
            $head_str = "活动名称,志愿者,联系电话,志愿时长,参与状态,报名时间\n";
            $head_str = iconv('utf-8','gb2312',$head_str);
            foreach ($datas as $k => $v){
                if($v['join_status'] == 1) $v['join_status'] = '已参与';
                if($v['join_status'] == 2) $v['join_status'] = '未参与';
                if($v['join_status'] == 3) $v['join_status'] = '待参与';

                $activity_datas = $mActivity->where("id={$v['activity_id']}")->fRow();
                $volunteer_datas = $mVolunteer->where("id={$v['volunteer_id']}")->fRow();
                $v['activity_id'] = $activity_datas['caption'];
                $v['volunteer_id'] = $volunteer_datas['username'];
                $v['mobile'] = $volunteer_datas['mobile'];

                $activity_id = iconv('utf-8','gb2312',$v['activity_id']); //中文转码
                $volunteer_id = iconv('utf-8','gb2312',$v['volunteer_id']);
                $mobile = iconv('utf-8','gb2312',$v['mobile']);
                $service_hour = iconv('utf-8','gb2312',$v['service_hour']);
                $join_status = iconv('utf-8','gb2312',$v['join_status']);
                $created_at = iconv('utf-8','gb2312',$v['created_at']);
                $head_str .= $activity_id.",".$volunteer_id.",".$mobile.",".$service_hour.",".$join_status.",".$created_at."\n"; //用引文逗号分开
            }
            $filename = date('Y-m-d').'.csv'; //设置文件名
            $mExport->export_csv($filename,$head_str);

        }

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
            if(!$manager->where("activity_id={$_POST['activity_id']} and volunteer_id={$_POST['volunteer_id']}")->update(array('join_status' => '3'))){
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