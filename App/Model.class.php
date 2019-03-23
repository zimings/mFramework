<?php
/**
 * model基类
 */

use mFramework\Core;

class Model
{
    protected static $mysql;

    protected function getMysql(){
        if (self::$mysql===null)
            self::$mysql = new Core\Mysql();
        return self::$mysql;
    }
    protected function truncateStr($str, $len, $replace = '...')
    {
        $str = strip_tags($str);
        if (mb_strlen($str) >= $len) {
            $ret = substr($str, 0, $len) . $replace;
        } else {
            $ret = $str;
        }
        return $ret;
    }

    /*更改某些信息后，更新session数据*/
    protected function updateSession()
    {
        $_SESSION['user'] = self::$mysql->getOneRow('*', 'user', array('uid' => $_SESSION['user']['uid']));
        $signature = self::$mysql->getOneRow('signature', 'userMore', array('uid' => $_SESSION['user']['uid']));
        $_SESSION['user']['signature'] = $signature['signature'];
    }

    /*发送验证码的方法*/
    protected function sendVerification($phone)
    {
        function getData($url)
        {
            $headerArray = array("Content-type:application/json;", "Accept:application/json");
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($url, CURLOPT_HTTPHEADER, $headerArray);
            $output = curl_exec($ch);
            curl_close($ch);
            return $output;
        }

        $result = self::$mysql->getRows('phone', 'user', array('phone' => $phone));
        if (empty($result)) {
            $url = 'http://' . $_SERVER['SERVER_NAME'] . __WEB__ . '?api=SMS&a=verification&phone=' . $phone;
            $ret = getData($url);
            $obj = json_decode($ret);
            return array('result' => $obj->result, 'mes' => $obj->errmsg, 'code' => $obj->code);
        } else {
            return array('result' => '手机号已被使用', 'mes' => '手机号已被使用', 'code' => '201');
        }
    }

    /*验证验证码是否正确的方法*/
    protected function checkVerification($userCode, $code, $time)
    {
        if ($userCode == $code && !empty($userCode) && !empty($code)) {
            if (time() - $time < 100) {
                return '{"code":"900"}';
            } else {
                return '{"code":"901","mes":"The verification code has expired!"}';
            }
        } else {
            return '{"code":"902","mes":"Verification code error"}';
        }
    }

    /*得到文章列表*/
    protected function pages($data, $num = 8)
    {
        $len = count($data);
        $pageNow = 1;
        if (isset($_GET['page'])) {
            $pageNow = $_GET['page'];
        }
        // 处理URL，正确传递page参数
        $url = __WEB__ . '?' . $_SERVER['QUERY_STRING'];
        if (isset($_GET['page'])) {
            if (isset($_GET['c'])) {
                $url = substr($url, 0, strpos($url, '&page'));
                $url = $url . '&';
            } else {
                $url = substr($url, 0, strpos($url, 'page'));
            }
        } else {
            if (isset($_GET['c'])) {
                $url = $url . '&';
            }
        }
        // 计算总页数
        $page = $len / $num;
        if (is_float($page)) {
            $page = (int)$page + 1;
        }
        // 处理展现内容
        $contentArr = $data;
        $content = array();
        for ($i = 1; $i <= $page; $i++) {
            for ($j = $len - 1; $j > $len - $num - 1; $j--) {
                $content[$i] = $content[$i] . $data[$j];
                unset($contentArr[$j]);
            }
            $len = $j + 1;
            $data = $contentArr;
        }
        // 处理上一页下一页
        $last = $pageNow - 1;
        $next = $pageNow + 1;
        if ($last == 0) {
            $last = 1;
        }
        if ($next > $page) {
            $next = $page;
        }
        // 判断当前页码是否加上class="active"
        switch ($pageNow) {
            case 1:
                $activePage[1] = 'class="active"';
                break;

            default:
                $activePage[$pageNow] = 'class="active"';
                break;
        }
        $pageList = '';
        for ($i = 2; $i < $page + 1; $i++) {// $i是从第二页开始的，所以$page+1是循环次数
            $pageList = $pageList . '<li ' . $activePage[$i] . '><a href="' . $url . 'page=' . $i . '">' . $i . '</a></li>';
        }
        $ret = $content[$pageNow] . '
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li>
                    <a href="' . $url . 'page=' . $last . '" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li ' . $activePage[1] . '><a href="' . $url . 'page=1">1</a></li>
                ' . $pageList . '
                <li>
                    <a href="' . $url . 'page=' . $next . '" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>';
        return $ret;
    }
}
