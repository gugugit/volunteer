<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 16/10/18
 * Time: 16:34
 */

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 * 引导程序，它是Yaf提供的一个全局配置的入口, 在Bootstrap中, 你可以做很多全局自定义的工作.
 * 方法在Bootstrap类中的定义出现顺序, 决定了它们的被调用顺序.
 */

class Bootstrap extends Yaf_Bootstrap_Abstract{

    /**
     * 配置
    */
    public function _initConfig(){
        $this->config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set("config",$this->config);
        define('PATH_APP', $this->config->application->directory);
        define('PATH_TPL', PATH_APP . '/views');

    }

    public function _initDefaultName(Yaf_Dispatcher $dispatcher){

        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");

    }

//Yaf支持用户定义插件来扩展Yaf的功能, 这些插件都是一些类.
//它们都必须继承自Yaf_Plugin_Abstract. 插件要发挥功效, 也必须现实的在Yaf中进行注册,
//然后在适当的实际, Yaf就会调用它.
//注册插件
    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
//        $user = new UserPlugin();
//        $hai = new HaiPlugin();
//        $love = new GuohailongPlugin();
//        $dispatcher->registerPlugin($user);
//        $dispatcher->registerPlugin($hai);
//        $dispatcher->registerPlugin($love);
    }
//    在这里注册自己的路由协议,默认使用简单路由 可以在这里定义我们的自己的路由协议，也可以在application.ini.
//    直接定义
    public function _initRoute(Yaf_Dispatcher $dispatcher){

        // $router = $dispatcher->getInstance()->getRouter();
//        $route = new Yaf_Route_Simple('m','c','a');
//        $router->addRoute('simple',$route);
        // $router->addConfig(Yaf_Registry::get("config")->routes);
    }
//    在这里注册自己的控制器,如smarty
    public function _initView(Yaf_Dispatcher $dispatcher){
//        $dispatcher ->getInstance() ->disableView();
    }



}
