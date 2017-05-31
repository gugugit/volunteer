<?php
class RankController extends Controller_Base{

    /**
     * 排行榜首页
    */
    public function rankingAction(){
        $m_av = new ActivolunteerModel();

        #日排名
        $today_date =  date('Y-m-d');
        $tomorrow_date =  date('Y-m-d',strtotime('+1 day'));
        $day_datas = $m_av->query("select v.username,av.service_hour from activity_volunteer av,volunteer v where av.join_status = 1 and av.updated_at>'$today_date' and av.updated_at<'$tomorrow_date' and av.volunteer_id = v.id order by av.service_hour desc limit 5");
        $this->assign('day_datas',$day_datas);

        #周排名
        $week_ago = date('Y-m-d',strtotime('-1 week'));
        $week_now =  date('Y-m-d');
        $week_datas = $m_av->query("select v.username,av.week_sum_hour from (select volunteer_id,sum(service_hour) week_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$week_ago' and updated_at<'$week_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.week_sum_hour desc limit 5");
        $this->assign('week_datas',$week_datas);

        #月排名
        $month_ago = date('Y-m-d',strtotime('-1 month'));
        $month_now =  date('Y-m-d');
        $month_datas = $m_av->query("select v.username,av.month_sum_hour from (select volunteer_id,sum(service_hour) month_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$month_ago' and updated_at<'$month_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.month_sum_hour desc limit 5");
        $this->assign('month_datas',$month_datas);

        #季度排名
        $quarter_ago = date('Y-m-d',strtotime('-3 month'));
        $quarter_now =  date('Y-m-d');
        $quarter_datas = $m_av->query("select v.username,av.quarter_sum_hour from (select volunteer_id,sum(service_hour) quarter_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$quarter_ago' and updated_at<'$quarter_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.quarter_sum_hour desc limit 5");
        $this->assign('quarter_datas',$quarter_datas);

        #年排名
        $year_ago = date('Y-m-d',strtotime('-1 year'));
        $year_now =  date('Y-m-d');
        $year_datas = $m_av->query("select v.username,av.year_sum_hour from (select volunteer_id,sum(service_hour) year_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$year_ago' and updated_at<'$year_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.year_sum_hour desc limit 5");
        $this->assign('year_datas',$year_datas);

    }

    /**
     * 排行榜详情页-日
    */
    public function detaildayAction(){
        $m_av = new ActivolunteerModel();
        $today_date =  date('Y-m-d');
        $tomorrow_date =  date('Y-m-d',strtotime('+1 day'));
        $day_datas = $m_av->query("select v.username,v.content,av.service_hour from activity_volunteer av,volunteer v where av.join_status = 1 and av.updated_at>'$today_date' and av.updated_at<'$tomorrow_date' and av.volunteer_id = v.id order by av.service_hour desc");
        $this->assign('day_datas',$day_datas);
    }

    /**
     * 排行榜详情页-周
     */
    public function detailweekAction(){
        $m_av = new ActivolunteerModel();
        $week_ago = date('Y-m-d',strtotime('-1 week'));
        $week_now =  date('Y-m-d');
        $week_datas = $m_av->query("select v.username,v.content,av.week_sum_hour from (select volunteer_id,sum(service_hour) week_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$week_ago' and updated_at<'$week_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.week_sum_hour desc");
        $this->assign('week_datas',$week_datas);

    }

    /**
     * 排行榜详情页-月
     */
    public function detailmonthAction(){
        $m_av = new ActivolunteerModel();
        $month_ago = date('Y-m-d',strtotime('-1 month'));
        $month_now =  date('Y-m-d');
        $month_datas = $m_av->query("select v.username,v.content,av.month_sum_hour from (select volunteer_id,sum(service_hour) month_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$month_ago' and updated_at<'$month_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.month_sum_hour desc");
        $this->assign('month_datas',$month_datas);

    }

    /**
     * 排行榜详情页-季
     */
    public function detailquarterAction(){
        $m_av = new ActivolunteerModel();
        $quarter_ago = date('Y-m-d',strtotime('-3 month'));
        $quarter_now =  date('Y-m-d');
        $quarter_datas = $m_av->query("select v.username,v.content,av.quarter_sum_hour from (select volunteer_id,sum(service_hour) quarter_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$quarter_ago' and updated_at<'$quarter_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.quarter_sum_hour desc");
        $this->assign('quarter_datas',$quarter_datas);
    }

    /**
     * 排行榜详情页-年
     */
    public function detailyearAction(){
        $m_av = new ActivolunteerModel();
        $year_ago = date('Y-m-d',strtotime('-1 year'));
        $year_now =  date('Y-m-d');
        $year_datas = $m_av->query("select v.username,v.content,av.year_sum_hour from (select volunteer_id,sum(service_hour) year_sum_hour from activity_volunteer where join_status = 1 and updated_at>'$year_ago' and updated_at<'$year_now' group by volunteer_id) av,volunteer v where av.volunteer_id = v.id order by av.year_sum_hour desc");
        $this->assign('year_datas',$year_datas);
    }

}