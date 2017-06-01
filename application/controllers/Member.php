<?php
class MemberController extends Controller_Base{

    /**
     * 会员首页
    */
    public function indexAction(){
        $mMember = new MemberModel();
        if('POST' == $_SERVER['REQUEST_METHOD']){
            if(empty($_POST['soso'])||!Helper\Validate::safe($_POST['soso'])){
                Msg::js("请输入成员姓名");
            }
            $datas = $this->_list($mMember,"L=8&name=LIKE*{$_POST['soso']}*");
            if(empty($datas)){
                Msg::js("没有您要查询的成员信息，请重新输入");
            }
            $this->assign('datas',$datas);
        }else{
            $datas = $this->_list($mMember,'L=8');
            $this->assign('datas',$datas);
        }
    }

    /**
     * 详情页
    */
    public function memberdetailAction($id = 0){
        $model_member = new MemberModel();
        if(!($id = (int)$id) || !$value = $model_member->fRow($id)){
           Msg::js("sorry～你访问的页面被外星人带走啦");
        }
        $this->assign("value",$value);
    }

}