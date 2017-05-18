<?php
class IntroduceController extends Controller_Base{

    public function indexAction(){
        $model_intro = new IntroduceModel();
        $values = $this->_list($model_intro);

        $this->assign("values",$values);
    }
}