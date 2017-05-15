<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 16:56
 */

class Helper_Layout implements Yaf_View_Interface{

    /**
     * 模板引擎，默认是 Yaf_View_Simple
     * @var Yaf_View_Simple
     */
    public $engine;

    /**
     * 选项
     * @var array
     */
    protected $options = array();

    /**
     * 模板目录
     * @var string
     */
    protected $layout_path;

    /**
     * 模板名（不带后缀）
     * @var string
     */
    protected $layout;

    /**
     * 处理动作的数据
     * @var string
     */
    protected $content;

    /**
     * 赋值模板变量
     * @var array
     */
    protected $tpl_vars = array();

    /**
     * 模板路径
     * @var string
     */
    protected $tpl_dir;

    /**
     * 构造函数
     * @param string $path 布局模板路径
     * @param array $options 键/值对 分配到模板引擎
     */
    public function __construct($path, $options = array())
    {
        $this->layout_path = $path;
        $this->options = $options;
    }

    /**
     * 模板引擎实例
     * @return Yaf_View_Simple
     */
    protected function engine()
    {
        $this->engine || $this->engine = new Yaf_View_Simple($this->tpl_dir, $this->options);
        return $this->engine;
    }

    /**
     * 设置布局模板路径
     * @param string $path 路径
     * @return bool
     * @throws Exception
     */
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
            $this->tpl_dir = $path;
            $this->engine()->setScriptPath($path);
            $this->layout_path = $path . "/layouts";
            return true;
        }
        throw new \Exception("Invalid path: {$path}");
    }

    /**
     * 取布局模板路径
     * @return string
     */
    public function getScriptPath()
    {
        return $this->engine()->getScriptPath();
    }

    /**
     * 设置模板名（不带后缀）
     * @param string $name
     */
    public function setLayout($name)
    {
        $this->layout = $name;
    }

    /**
     * 取模板名（不带后缀）
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * 设置布局文件夹路径
     * @param $path
     */
    public function setLayoutPath($path)
    {
        $this->layout_path = $path;
    }

    /**
     * 获取与文件名和扩展名完整布局路径
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->layout_path . "/" . $this->layout . ".phtml";
    }

    /**
     * 注册变量到模板
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->assign($name, $value);
    }

    /**
     * 判断变量是否存在
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return (null !== $this->engine()->$name);
    }

    /**
     * 删除模板变量
     * @param string $name
     */
    public function __unset($name)
    {
        $this->engine()->clear($name);
    }

    /**
     * 注册变量到模板
     * @param string|array $name
     * @param mixed $value
     * @return bool|void
     */
    public function assign($name, $value = null)
    {
        if(is_array($name)){
            foreach ($name as $k => $v) {
                $this->tpl_vars[$k] = $v;
            }
            $this->engine()->assign($name);
        } else {
            $this->tpl_vars[$name] = $value;
            $this->engine()->assign($name, $value);
        }
    }

    /**
     * 注册变量到模板(引用方式，推荐)
     */
    public function assignRef($name, &$value)
    {
        $this->tpl_vars[$name] = $value;
        $this->engine()->assignRef($name, $value);
    }

    /**
     * 清除所有分配的变量
     * @return void
     */
    public function clearVars()
    {
        $this->tpl_vars = array();
        $this->engine()->clear();
    }

    /**
     * 调用并渲染 模板
     * @param string $tpl 调用模板
     * @param array $tpl_vars 注册变量到模板
     * @return string
     */
    public function render($tpl, $tpl_vars = array())
    {

        $tpl_vars = array_merge($this->tpl_vars, $tpl_vars);
        $this->content = $this->engine()->render($tpl, $tpl_vars);
        if (null == $this->layout) {
            return $this->content;
        }
        $ref = new ReflectionClass($this->engine());
        $prop = $ref->getProperty('_tpl_vars');
        $prop->setAccessible(true);
        $view_vars = $prop->getValue($this->engine());
        $tpl_vars = array_merge($tpl_vars, $view_vars);
        $tpl_vars['_content_'] = $this->content;
        return $this->engine()->render($this->getLayoutPath(), $tpl_vars);
    }

    /**
     * 渲染模板
     * @param $tpl
     * @param array $tpl_vars
     * @return bool|void
     */
    public function display($tpl, $tpl_vars = array())
    {
        echo $this->render($tpl, $tpl_vars);
    }

}