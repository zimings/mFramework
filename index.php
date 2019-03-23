<?php
session_start();
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");

define("DS", DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(__FILE__) . DS);
require __ROOT__ . 'Core' . DS . 'Bootstrap.php';//加载引导文件

use \mFramework\Core;

Core\Bootstrap::run();