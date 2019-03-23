<?php

require __ROOT__ . 'Core/Router.php';

use mFramework\Core;

/**
 * API基类
 */
class API
{
    public static function run()
    {
        self::addRoter();
        // 初始化
        static::autoload();
        static::disPath(Core\Router::getController(), Core\Router::getAction());
    }

    private static function addRoter()
    {
        $rules = array(
            'upload' => 'upload/uploadImg',
        );
        //加载路由
        $Routes = new Core\Router();
        $Routes->rules($rules);
    }

    private static function disPath($c, $a)
    {
        // 获取控制器名称
        $controller_name = $c . 'Controller';
        // 获取动作名称
        $action_name = $a . 'Action';
        // 实例化控制器对象
        $controller = new $controller_name();
        // 调用方法
        $controller->$action_name();
    }

    // 自动加载函数
    private static function load($classname)
    {
        if (substr($classname, -10) == 'Controller') {
            // 载入控制器
            include __API__ . API . DS . "{$classname}.class.php";
        } elseif (substr($classname, -5) == 'Model') {
            include __API__ . API . DS . "Model" . DS . "{$classname}.class.php";
        }
    }

    // 注册为自动加载函数
    private static function autoload()
    {
        $arr = array(__CLASS__, 'load');
        spl_autoload_register($arr);
    }

    public function instance($name, $modelPath = __MODEL__)
    {
        include $modelPath . $name . 'Model.class.php';
        $modelName = $name . 'Model';
        $model = new $modelName;
        return $model;
    }

    public function replaceSpecialSymbols($strParam)
    {
        $regex = '/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/';
        $str = preg_replace($regex, '', $strParam);
        return $str;
    }

    public function fileTypeJudge($filename)
    {
        $file = fopen($filename, "rb");
        $bin = fread($file, 2); //只读2字节
        fclose($file);
        $strInfo = @unpack("c2chars", $bin);
        $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
        switch ($typeCode) {
            case 7790:
                $fileType = 'exe';
                break;
            case 7784:
                $fileType = 'midi';
                break;
            case 8297:
                $fileType = 'rar';
                break;
            case 255216:
                $fileType = 'jpg';
                break;
            case 7173:
                $fileType = 'gif';
                break;
            case 6677:
                $fileType = 'bmp';
                break;
            case 13780:
                $fileType = 'png';
                break;
            default:
                $fileType = 'unknown' . $typeCode;
                break;
        }
        //Fix
        if ($strInfo['chars1'] == '-1' && $strInfo['chars2'] == '-40') {
            return 'jpg';
        }
        if ($strInfo['chars1'] == '-119' && $strInfo['chars2'] == '80') {
            return 'png';
        }
        return $fileType;
    }
}