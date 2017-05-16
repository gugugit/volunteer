<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 10:08
 * 基础类
 */
abstract class Controller_Base extends Yaf_Controller_Abstract{
    /**
     * 布局
     * @var string
     */
    protected $layout;

    /**
     * SESSION
     * @var Session
     */
    protected $session;

    /**
     * 配置
     * @var Yaf_Config_Ini
     */
    protected $config;

    /**
     * 开启 SESSION : 1
     * 必须登录 : 2
     * 管理员 : 5
     */
    protected $_auth = 1;

    /**
     * 构造函数
     */
    protected function init()
    {
        #全局配置
        $this->config = Yaf_Application::app()->getConfig();

        # Request & Ajax
        $this->requests = $this->getRequest();
        define('IS_AJAX', $this->requests->isXmlHttpRequest());
        # 开启 SESSION
        if (1 & $this->_auth) {
            $this->session = Session::getInstance();
            $this->user = $this->session->user;
            (2 & $this->_auth) && $this->_v_login();
        }
        # Layout
        $this->layout && $this->getView()->setLayout($this->layout);
    }

    /**
     * 验证登录状态
     */
    protected function _v_login()
    {
        if (!$this->user) {
            @setcookie('USER', null, time() - 3600, '/');
            IS_AJAX? Msg::ajax('登录超时，请重新登录'): Msg::js('', '/volunteer/login');
        }
    }

    /**
     * 注册变量到模板
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return mixed
     */
    protected function assign($key, $value = '')
    {
        if (is_array($key)) {
            $this->_view->assign($key);
            return $key;
        }
        $this->_view->assign($key, $value);
        return $value;
    }

    /**
     * 渲染指定模板
     */
    protected function tpl($tpl)
    {
        if (false === strpos($tpl, '/')) {
            $tpl = PATH_TPL . '/tpl/' . $tpl . '.phtml';
        }
        $this->getView()->display($tpl);
        exit;
    }

    /**
     * SEO信息
     *
     * @param string $h1 页面主题
     * @param string $keywords meta keywords
     * @param string $description meta description
     * @param string $title meta title
     */
    protected function seo($h1, $keywords = '', $description = '', $title = '')
    {
        $seo = array('keywords' => $keywords, 'description' => $description, 'h1msg' => '');
        if (is_array($h1)) {
            $seo['h1'] = $h1[0];
            $seo['h1msg'] = $h1[1];
        } else {
            $seo['h1'] = $h1;
        }
        $seo['title'] = $title? $title: $seo['h1'];
        $this->getView()->assign($seo);
    }

    /**
     * 自动SEO
     */
    protected function seo_auto()
    {
        $func = new ReflectionMethod($this, $this->requests->action.'Action');
        $doc = $func->getDocComment();
        preg_match_all('/\* (.*?)\n/', $doc, $doc);
        $this->seo($doc[1][0]);
    }

    /**
     * 搜索
     *
     * @param string $default 默认搜索字段
     * @param array $map 字段映射
     */
    protected function search($default, $map)
    {
        $this->search = $map;
        isset($_GET['field']) || $_GET['field'] = $default;
    }

    /**
     * 注册全局变量到模板（魔术方法）
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->$key = $value;
        $this->getView()->assignRef($key, $value);
    }

    /**
     * 数据表名 转 表对象
     *
     * @param Db_Mysql $table 表名
     */
    protected function _table(&$table)
    {
        if (is_string($table)) {
            $table = (strpos($table, '_')? str_replace(' ', '_', ucwords(str_replace('_', ' ', $table))): ucwords($table)) . 'Model';
            $table = new $table();
        }
    }

    /**
     * 显示、保存、添加
     *
     * @param Db_Mysql $table 表名或表对象
     * @param array $datas 数据
     * @param array $fields 过滤字段
     *
     * @return false:数据库错误, 0:无操作, 1+:保存并返回ID
     */
    protected function _save($table, $datas = array(), $fields = array(), $default = 3, $useAjax = true)
    {
        $this->_table($table);
        $this->fields = $fields? $fields: $table->getFields();
        $_GET[$table->pk] = isset($_REQUEST[$table->pk])? intval($_REQUEST[$table->pk]): 0;
        # 处理POST提交
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            if ($default) {
                if (!$_GET[$table->pk] && (1 & $default)) {
                    $datas['created_at'] = $_SERVER['REQUEST_TIME'];
                    $datas['create_ip'] = USER_IP;
                }
                if (2 & $default) {
                    $datas['updated_at'] = $_SERVER['REQUEST_TIME'];
                    $datas['update_ip'] = USER_IP;
                }
            }
            $datas || $datas = \Helper\Field::validates($this->fields);
            # 修改记录
            if ($_GET[$table->pk]) {
                $datas[$table->pk] = $_GET[$table->pk];
                $table->update($datas);
                $id = &$_GET[$table->pk];
            } # 新增记录
            else {
                if (isset($datas[$table->pk])) unset($datas[$table->pk]);
                $id = $table->insert($datas);
            }
            $useAjax && IS_AJAX && Msg::ajax('操作成功', 1);
            return empty($id)? false: $id;
        }
        $this->data = $_GET[$table->pk]? $table->fRow($_GET[$table->pk]): array();
        return 0;
    }

    /**
     * 数据列表
     *
     * @param Db_Mysql $table 表名
     * @param string $conn 查询条件 L=条数 OB=排序 字段=值
     * @param bool $page
     *
     * @return array
     */
    protected function _list($table, $conn = '', $page = true)
    {
        $this->_table($table);
        $this->fields = $table->getFields();
        # 自动搜索
        if (!empty($_GET['field']) && !empty($_GET['kw']) && defined('IS_ADMIN')) {
            empty($conn) || $conn .= '&';
            # like 搜索
            if (false === strpos($_GET['field'], '*')) {
                $conn .= $_GET['field'] . '=' . $_GET['kw'];
            } else {
                $conn .= str_replace('*', '', $_GET['field']) . '=LIKE *' . $_GET['kw'] . '*';
            }
        }
        parse_str($conn, $conn);
        # 查询条数
        if (isset($conn['L'])) {
            $limit = $conn['L'];
            unset($conn['L']);
        } else {
            $limit = 10;
        }
        # 排序
        if (isset($conn['OB'])) {
            $orderby = $conn['OB'];
            unset($conn['OB']);
        } else {
            $orderby = $table->pk . ' DESC';
        }
        # Where 条件
        if (!empty($table->options['where'])) {
            $where = $table->options['where'];
        } else {
            $where = array();
            foreach ($conn as $k1 => $v1) {
                if (0 === strpos($k1, 'SQL')) {
                    # SQL: SQL=abc
                    $where[] = "$v1";
                } elseif (0 === strpos($v1, 'IN')) {
                    # IN: field=IN(1,2,3) 将转换为 field IN(1,2,3)
                    $where[] = "$k1 $v1";
                } elseif (0 === strpos($v1, 'LIKE')) {
                    # LIKE：field=LIKE abc* 将转换为 field LIKE 'abc%'
                    if (!preg_match("/^[0-9a-zA-Z\x80-\xff@_\/*.-]+$/", $v1 = substr($v1, 5))) {
                        exit;
                    }
                    $v1 = str_replace('*', '%', $v1);
                    $where[] = "$k1 LIKE '$v1'";
                } elseif (0 === strpos($v1, 'BETWEEN')) {
                    # BETWEEN：field=BETWEEN 1AND2 将转换为 field BETWEEN 1 AND 2
                    if (!preg_match("/^[0-9a-zA-Z\x80-\xff@_\/*.-]+$/", $v1 = substr($v1, 8))) {
                        exit;
                    }
                    $v1 = str_replace('AND', ' AND ', $v1);
                    $where[] = "$k1 BETWEEN $v1";
                } elseif (0 === strpos($v1, '>') || 0 === strpos($v1, '<')) {
                    # 大于、小于
                    $option = 0 === strpos($v1, '>')? '>': '<';
                    $v1 = substr($v1, 1);
                    $where[] = "$k1 $option $v1";
                } else {
                    $where[] = "$k1='$v1'";
                }
            }
            if ($where = join(' AND ', $where)) {
                $table->where($where);
            }
        }
        # 不带分页
        if (false === $page) {
            return $this->datas = $table->limit($limit)->order($orderby)->fList();
        }
        # 带分页查询
        $field = isset($table->options['field'])? $table->options['field']: '*';
        $this->page = new \Helper\Page($table->count(), $limit);
        # 列表数据
        return $this->datas = $this->page->total
            ? $table->fList(array('field' => $field, 'where' => $where, 'order' => $orderby, 'limit' => $this->page->limit()))
            : array();
    }

    /**
     * AJAX列表数据
     *
     * @param Db_Mysql $table 数据表
     * @param string $field 查询字段
     * @param string $conn 查询条件
     */
    protected function _ajax_list($table, $field, $conn, $msg = '', $data = array())
    {
        $this->_table($table);
        $table->field($field);
        $this->_list($table, $conn);
        $data['datas'] = $this->datas;
        $data['page'] = $this->page;
        # 回调函数
        if (isset($this->cb) && isset($this->cb['cb'])) {
            $this->{$this->cb['cb']}($msg, $data);
        }
        Msg::ajax($msg, 1, $data);
    }

    /**
     * 删除记录
     *
     * @param Db_Mysql $table 表对象
     * @param string $id 主键
     */
    protected function _del($table, $id)
    {
        if ($id) {
            $this->_table($table);
            $table->del($id) && Msg::js("删除成功");
        }
        Msg::js('删除失败');
    }

    /**
     * 验证 > 密码 & 重复密码
     */
    protected function _v_repw()
    {
        # 密码
        if ($msg = \Helper\Validate::is_passwd('pw', '登录密码', 8, 32)) {
            Msg::ajax($msg, 0, 'pw');
        }
        if ($msg = \Helper\Validate::is_passwd('repw', '重复登录密码', 8, 32)) {
            Msg::ajax($msg, 0, 'repw');
        }
        $_POST['pw'] == $_POST['repw'] || Msg::ajax('两次输入的密码必须一致', 0, 'repw');
    }

}