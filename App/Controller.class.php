<?php
/*
 * Controller基类
 */

class Controller
{
    public static $tpl;

    public function __construct()
    {
        self::$tpl = new Template();
    }

    public function render_once($data, $dataName = null)
    {
        if ($dataName == null) {
            self::$tpl->assign($data);
        } else {
            self::$tpl->assign($dataName, $data);
        }
    }

    public function render($template, $data = null, $dataName = null)
    {
        if ($dataName == null) {
            self::$tpl->assign($data);
        } else {
            self::$tpl->assign($dataName, $data);
        }
        self::$tpl->show($template);
    }

    public function getParm()
    {
        return \mFramework\Core\Router::getParm();
    }
}
