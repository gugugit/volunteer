<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/16
 * Time: 23:54
 */
class BaseController extends Controller_Base{

    /**
     * 志愿基地首页显示
    */
    public function indexAction(){

        $model_base = new BaseModel();

        $datas = $this->_list($model_base);

        $this->assign("datas",$datas);
    }

    /**
     * 基地详情页
    */
    public function basedetailAction($id = 0){

        $model_base = new BaseModel();
        if(!($id = (int)$id) || !$value = $model_base->fRow($id)){
            echo "您访问的页面不存在";//这里有问题bug。
        }
        $this->assign("value",$value);
    }
}