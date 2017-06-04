<?php
class ExportController extends Controller_Admin{

    public $layout='admin';
    /**
     * 信息导出首页
    */
    public function exportAction(){
        $this->seo('志愿者信息导出');
        $mVolunteer = new VolunteerModel();
        $mExport = new ExportModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){
            $result = $mVolunteer->query('select v.username,v.student_id,v.class,a.full_name,v.sex,v.service_hour from volunteer v,academy a where v.academy_id = a.id');
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