<?php
class Controller_Admin extends Controller_Base{

    #管理员
    protected $_auth = 5;

    /**
     * 构造函数
    */
    protected  function init()
    {
        parent::init();

        #角色验证
        $this->_role();

        #权限控制
    }

    /**
     * 角色验证 first
     * @param string $msg 提示消息
     */
    protected function _role($msg = '')
    {
        if (empty($this->user) || !($this->user['role'] & $this->_auth)) {
            Msg::js($msg, '/');
        }
        define('IS_ADMIN', 'admin' == $this->user['role']);
    }

    /** second
     * 角色验证
     * @param string $msg 提示消息
     */
    protected function _v_role($msg = '')
    {
        if(!(2 & $this->user['role'])){
            IS_AJAX? Msg::ajax($msg): Msg::js($msg, '/volunteer/login');
        }
        define('IS_ADMIN', 1);
    }

}
