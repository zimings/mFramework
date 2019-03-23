<?php

namespace mFramework\Core;
class AutoLoad
{
    public static function mvcLoader()
    {
        self::mvcAutoload();
    }

    //自动加载函数
    private static function mvcLoad($classname)
    {
        if (substr($classname, -10) == 'Controller') {
            //载入控制器
            include __CONTROLLER__ . "{$classname}.class.php";
        } elseif (substr($classname, -5) == 'Model') {
            include __MODEL__ . "{$classname}.class.php";
        }
    }

    //注册为自动加载函数
    private static function mvcAutoload()
    {
        $arr = array(__CLASS__, 'mvcLoad');
        spl_autoload_register($arr);
    }
}