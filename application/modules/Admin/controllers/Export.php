<?php
class ExportController extends Controller_Admin{

    public $layout='admin';
    /**
     * 学生志愿服务时间导出
    */
    public function exportAction(){
        $this->seo('志愿者信息导出');
        $mVolunteer = new VolunteerModel();
        $mExport = new ExportModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){

           if('all_class'==$_POST['class_id'] && 'all_academy' == $_POST['academy_id'] && empty($_POST['start_time']) && empty($_POST['end_time'])) {
               $sql = "select v.username,v.student_id,v.class,a.full_name,v.sex,v.service_hour from volunteer v,academy a where v.academy_id = a.id order by v.service_hour desc";
           }
           if(!empty($_POST['class_id']) && 'all_class'!=$_POST['class_id'] && !empty($_POST['academy_id']) && 'all_academy'!=$_POST['academy_id'] && empty($_POST['start_time'])
               && empty($_POST['end_time'])){
               $class_id = $_POST['class_id'];
               $academy_id = $_POST['academy_id'];
               $sql = "select v.username,v.student_id,v.class,a.full_name,v.sex,v.service_hour from volunteer v,academy a where v.academy_id = a.id and v.student_id like '$class_id%' and a.full_name like '%$academy_id%' order by v.service_hour desc";
           }
           if(!empty($_POST['class_id']) && 'all_class'!=$_POST['class_id'] && 'all_academy' == $_POST['academy_id'] && !empty($_POST['start_time']) && !empty($_POST['end_time'])){
               $class_id = $_POST['class_id'];
               $start_date = $_POST['start_time'];
               $end_date = $_POST['end_time'];
               $sql = "select v.username,v.student_id,v.class,a.full_name,v.sex,av.day_sum_hour service_hour from (select volunteer_id,sum(service_hour) day_sum_hour from activity_volunteer where  join_status = 1 and  updated_at>'$start_date' and updated_at<'$end_date' group by volunteer_id) av,volunteer v,academy a where av.volunteer_id = v.id and v.student_id like '$class_id%' and v.academy_id = a.id order by av.day_sum_hour desc";
           }

            if('all_class'==$_POST['class_id'] && !empty($_POST['academy_id']) && 'all_academy'!=$_POST['academy_id'] && !empty($_POST['start_time']) && !empty($_POST['end_time'])){
                $academy_id = $_POST['academy_id'];
                $start_date = $_POST['start_time'];
                $end_date = $_POST['end_time'];
                $sql = "select v.username,v.student_id,v.class,a.full_name,v.sex,av.day_sum_hour service_hour from (select volunteer_id,sum(service_hour) day_sum_hour from activity_volunteer where  join_status = 1 and  updated_at>'$start_date' and updated_at<'$end_date' group by volunteer_id) av,volunteer v,academy a where av.volunteer_id = v.id and a.full_name like '%$academy_id%' and  v.academy_id = a.id order by av.day_sum_hour desc";
           }

            if(!empty($_POST['class_id']) && 'all_class'!=$_POST['class_id'] && !empty($_POST['academy_id']) && 'all_academy'!=$_POST['academy_id'] && !empty($_POST['start_time']) && !empty($_POST['end_time'])){
                $class_id = $_POST['class_id'];
                $academy_id = $_POST['academy_id'];
                $start_date = $_POST['start_time'];
                $end_date = $_POST['end_time'];
                $sql = "select v.username,v.student_id,v.class,a.full_name,v.sex,av.day_sum_hour service_hour from (select volunteer_id,sum(service_hour) day_sum_hour from activity_volunteer where  join_status = 1 and  updated_at>'$start_date' and updated_at<'$end_date' group by volunteer_id) av,volunteer v,academy a where av.volunteer_id = v.id and v.student_id like '$class_id%' and  a.full_name like '%$academy_id%' and  v.academy_id = a.id order by av.day_sum_hour desc";
            }
            $result = $mVolunteer->query($sql);
            $head_str = "姓名,学号,班级,学院,性别,志愿时间\n";
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

    /**
     * 学生账号导入
    */
    public function importAction(){
        $this->seo('志愿者信息导入');
        $mImport = new ExportModel();
        $mVolunteer = new VolunteerModel();

        if('POST' == $_SERVER['REQUEST_METHOD']){
            $filename = $_FILES['file']['tmp_name'];
            if (empty ($filename)) {
              Msg::js("请选择要导入的CSV文件！");
            }
            $handle = fopen($filename, 'r');
            $result = $mImport->input_csv($handle); //解析csv
            $len_result = count($result);
            if($len_result==0){
                Msg::js("没有任何数据!");
                exit;
            }
            $data_values ='';
            for ($i = 1; $i < $len_result; $i++) { //循环获取各字段值
                $name = iconv('gb2312', 'utf-8', $result[$i][0]); //中文转码
                $student_id = iconv('gb2312', 'utf-8', $result[$i][1]);
                $salt = rand(100000, 999999);
                $password = VolunteerModel::saltpw($student_id,$salt);
                $data_values .= "('$name','$student_id','$password','$salt'),";
            }
            $data_values = substr($data_values,0,-1); //去掉最后一个逗号
            fclose($handle); //关闭指针

            $mVolunteer->query("insert into volunteer(username,student_id,password,salt)values $data_values");
            Msg::js('导入成功');
        }
    }
}