<?php

namespace mFramework\Core;
class Router
{
    private static $rules = array();
    private static $parms = array();
    private static $controller;
    private static $action;

    private function getRequest()
    {
        $request = str_replace(__WEB__, '', $_SERVER['REQUEST_URI']);
        return $request;
    }

    private function dealRequest($requests)
    {
        foreach (array_keys(self::$rules) as $rule) {
            if ($rule !== '//') {
                //将uri中的参数提取到self::$parms
                $flag = preg_match($rule, $requests, $preg_res);
                if ($flag) {
                    self::$parms = array(self::$parms[$rule][0] => '');
                    $keys = array_keys(self::$parms);
                    $len = count($keys);
                    for ($i = 0; $i < $len; $i++) {
                        $key = $keys[$i];
                        self::$parms[$key] = $preg_res[$i + 1];
                    }
                    //判断控制器和动作
                    $this->dealRule(self::$rules[$rule]);
                }
            } else {
                //判断控制器和动作
                $this->dealRule(self::$rules[$rule]);
            }
        }
        $request_get = substr($requests, strpos($requests, '?') + 1);
        parse_str($request_get, self::$parms['get']);
    }

    private function dealRule($rules)
    {
        if (substr($rules, 0, 1) == ':') {
            $path = $this->getParm();
            $file = file_get_contents(substr($rules, 1) . $path['path']);
            $type = trim(strrchr($path['path'], '.'), '.');
            $type = $type == 'js' ? 'javascript' : $type;
            header('Content-type: text/' . $type);
            echo $file;
            exit();
        } else {
            $rule = explode('/', $rules);
            self::$controller = $rule[0];
            self::$action = $rule[1];
        }
    }

    public function rules($rules)
    {
        foreach (array_keys($rules) as $rule) {
            $val = $rules[$rule];
            $parm = array();
            //将大括号中内容取出作为数组键
            preg_match_all('/[^\{]*\{([^\}]+)\}/', $rule, $preg_res);
            foreach ($preg_res[1] as $name) {
                $parm[] = $name;
                //删除大括号中的内容
                $rule = str_replace('{' . $name . '}', '{}', $rule);
            }
            $r = array(
                '/',
                '{',
                '}'
            );
            $p = array(
                '\/',
                '(\S+)',
                ''
            );
            $rule = '/' . str_replace($r, $p, $rule) . '/';
            self::$rules[$rule] = $val;//转换成正则表达式的路由规则
            self::$parms[$rule] = $parm;
        }
        $requests = $this->getRequest();
        $this->dealRequest($requests);
    }

    public static function getParm()
    {
        return self::$parms;
    }

    public static function getController()
    {
        return self::$controller;
    }

    public static function getAction()
    {
        return self::$action;
    }
}