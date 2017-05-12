<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/11/4
 * Time: 11:02
 */
class UserController extends Yaf_Controller_Abstract{
    public function showAction(){

        $userId = $this->getRequest()->get("userId");
        $userModel = new UserModel();
        echo $userModel->getUserInfo($userId);
//        $params = $this->getRequest()->getParams();
//        $userId = $params['userId'];
//        echo "USERID:". $userId;

//        var_dump($params);
//        echo "<br />";
//        echo $_SERVER['REQUEST_URI'];
        return false;
    }

    public function loginAction(){
        echo "login";

        return false;

    }
    public function registerAction(){
        echo "register";

        return false;

    }


    public function hellovAction(){
        return false;
    }
}