<?php
class MemberController extends Controller_Base{

    /**
     * 会员首页
    */
    public function indexAction(){
        $mMember = new MemberModel();
        $datas = $this->_list($mMember,'L=8');
        $this->assign('datas',$datas);
    }

    /**
     * 详情页
    */
    public function memberdetailAction($id = 0){
        $model_member = new MemberModel();
        if(!($id = (int)$id) || !$value = $model_member->fRow($id)){
            echo "您访问的页面不存在";//这里有问题bug。
        }
        $this->assign("value",$value);
    }

}