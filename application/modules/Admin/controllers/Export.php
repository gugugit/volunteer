<?php
class ExportController extends Controller_Admin{

    public $layout='admin';
    /**
     * 信息导出首页
    */
    public function indexAction(){
        $this->seo('志愿者信息导出');
        $mVolunteer = new VolunteerModel();
        $mExport = new ExportModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){
            $result = $mVolunteer->query('select v.username,v.student_id,v.class,a.full_name,v.sex,v.service_hour from volunteer v,academy a where v.academy_id = a.id');
            $head_str = "姓名,学号,班级,学院,性别,志愿服务时间\n";
            $head_str = iconv('utf-8','gb2312',$head_str);
            foreach ($result as $k => $v){
                $v['sex'] = $v['sex']?'男':'女';
                $username = iconv('utf-8','gb2312',$v['username']); //中文转码
                $student_id = iconv('utf-8','gb2312',$v['student_id']);
                $class = iconv('utf-8','gb2312',$v['class']);
                $full_name = iconv('utf-8','gb2312',$v['full_name']);
                $sex = iconv('utf-8','gb2312',$v['sex']);
                $service_hour = iconv('utf-8','gb2312',$v['service_hour']);
                $head_str .= $username.",".$student_id.",".$class.",".$full_name.",".$sex.",".$service_hour."\n"; //用引文逗号分开
            }
            $filename = date('Y-m-d').'.csv'; //设置文件名
            $mExport->export_csv($filename,$head_str);
        }
    }
}