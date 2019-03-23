<?php

namespace mFramework\Core;

//自动加载类
require 'AutoLoad.php';
//路由
require 'Router.php';
//模板引擎
require 'TemplateEngine' . DS . 'Template.php';
//加载mysql class
require 'Mysql.class.php';

class Bootstrap
{
    public static $Config;

    public static function run()
    {
        self::loadConfig();
        self::loadPath();
        //控制器
        require APP_ROOT . 'Controller.class.php';
        //模型
        require APP_ROOT . 'Model.class.php';
        require APP_ROOT . 'setting.php';
    }

    public function loadRoutes($rules)
    {
        //加载路由
        $Routes = new Router();
        $Routes->rules($rules);
        //自动加载
        AutoLoad::mvcLoader();
        //调用方法
        self::loadMethod(Router::getController(), Router::getAction());
    }

    private static function loadMethod($c, $a)
    {
        //获取控制器名称
        $controller_name = $c . 'Controller';
        //获取动作名称
        $action_name = $a . 'Action';
        //实例化控制器对象
        $controller = new $controller_name();
        //调用方法
        $controller->$action_name();
    }

    private static function loadConfig()
    {
        //读取配置文件
        self::$Config = require 'config' . DS . 'Config.php';
        // 是否开启dBug
        if (self::$Config['dBug']) {
            ini_set("display_errors", "On");
            error_reporting(E_ALL ^ E_NOTICE);
        } else error_reporting(0);
    }

    private static function loadPath()
    {
        //AppRoot
        define('APP_ROOT', __ROOT__ . 'App' . DS);
        //App path
        define('__APP__', APP_ROOT . self::$Config['Apps'][0] . DS);
        //自定义标签
        define('__LABEL__', APP_ROOT . 'Label' . DS);
        //API
        define('__API__', __ROOT__ . 'API' . DS);
        //定义公共文件
        define('__PUB__', __ROOT__ . 'Public' . DS);
        //静态文件路径
        define('__STATIC__', __ROOT__ . 'Static' . DS);
        //MODEL文件夹
        define('__MODEL__', __APP__ . 'Model' . DS);
        //CONTROLLER文件夹
        define('__CONTROLLER__', __APP__ . 'Controller' . DS);
        //VIEW文件夹
        define('__VIEW__', __APP__ . 'View' . DS);
        //域名下网站根目录
        $WEB = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        define('__WEB__', $WEB == '/' ? '/' : $WEB . '/');
    }
}
