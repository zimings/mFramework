<?php
//页面路径
define('__CSS__', __WEB__ . 'Static/css/');
define('__IMG__', __WEB__ . 'Static/images/');
define('__JS__', __WEB__ . 'Static/js/');
define('__LIB__', __WEB__ . 'Static/library/');

$Bootstrap = new mFramework\Core\Bootstrap();
$rules = array(
    '' => 'index/index',
);
$Bootstrap->loadRoutes($rules);