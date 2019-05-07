<?php
/*
 * 数据库查询封装
 */

namespace mFramework\Core;

use mFramework\Core\Bootstrap;
use mysqli;

class Mysql
{
    private static $mysqli;

    public function __construct($config = null)
    {
        if (empty($config))
            $config = Bootstrap::$Config['DbInfo'];
        self::$mysqli = $this->connect($config);
    }

    /**
     * 连接数据库函数
     */
    private function connect($config)
    {
        $mysqli = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);
        if ($mysqli->connect_errno) { //连接成功errno应该为0
            die('Connect Error:' . $mysqli->connect_error);
        }
        $mysqli->set_charset('utf8');
        return $mysqli;
    }

    /**
     * 返回数据
     *
     * @param string $sql sql语句
     * @return bool
     */
    private function returnData(string $sql): bool
    {
        $result = self::$mysqli->query($sql);
        self::$mysqli->close();
        if ($result) {
            return true;
        } else {
            $err = self::$mysqli->errno . ":" . self::$mysqli->error;
            echo "<script>console.log('error:{$err}');</script>";
            return false;
        }
    }

    /**
     * 对where进行处理
     *
     * @param array $where 过滤条件
     * @param string $connect 连接符
     * @return string
     */
    private function jointWhere(array $where = null, string $connect = ' = '): string
    {
        if (empty($where)) return '';
        $len = 0;
        foreach ($where as $key => $value) {
            $where[$key] = self::$mysqli->real_escape_string($value);
            $len++;//数组长度
        }
        if ($len == 1) {//数组长度为1则返回*** $connect '***'否则返回*** $connect '***' and *** $connect '***'
            $where = array_keys($where)[0] . $connect . '\'' . array_values($where)[0] . '\'';
        } else {
            $sets = array();
            foreach ($where as $key => $value) {
                $kstr = '`' . $key . '`';
                $vstr = '\'' . $value . '\'';
                array_push($sets, $kstr . $connect . $vstr);
            }
            $where = implode(' and ', $sets);
        }
        return $where;
    }

    /**
     * 查询一条数据
     *
     * @param mixed $data 查询内容
     * @param string $table 数据表
     * @param array $where 过滤条件
     * @return array
     */
    public function getOneRow($data, string $table, array $where)
    {
        $result = $this->select($data, $table, $where, 0, 1);
        return $result[0];
    }

    /**
     * 查询一条数据
     *
     * @param mixed $data 查询内容
     * @param string $table 数据表
     * @param array $where 过滤条件
     * @return array
     */
    public function getRows($data, string $table, array $where = null, int $offset = 0, int $limit = 1000)
    {
        $result = $this->select($data, $table, $where, $offset, $limit);
        return $result;
    }

    /**
     * 搜索数据
     *
     * @param mixed $data 搜索内容
     * @param string $table 数据表
     * @param array $where 过滤条件
     * @return array
     */
    public function search($data, string $table, array $where)
    {
        $result = $this->select($data, $table, $where, 0, 1000, ' like ');
        return $result;
    }

    /**
     * 查询数据
     *
     * @param mixed $data 查询内容
     * @param string $table 数据表
     * @param array $where 过滤条件
     * @param int $offset 偏移量
     * @param int $limit 行数限制
     * @param string $connectStr 限定条件连接符
     * @return array
     */
    private function select($data, string $table, array $where = null, int $offset = 0, int $limit = 1000, string $connectStr = ' = ')
    {
        $table = self::$mysqli->real_escape_string($table);
        $where = $this->jointWhere($where, $connectStr);
        if (is_array($data)) {//判断传入数据是否为数组
            $data = implode(',', $data);
            $data = self::$mysqli->real_escape_string($data);
        } else {
            $data = self::$mysqli->real_escape_string($data);
        }
        if (empty($where)) {//判断是否有限定条件
            $sql = "SELECT {$data} FROM {$table} LIMIT {$offset},{$limit}";
        } else {
            $sql = "SELECT {$data} FROM {$table} WHERE {$where} LIMIT {$offset},{$limit}";
        }
        $mysqli_result = self::$mysqli->query($sql);
        $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
        $mysqli_result->close();
        return $result;
    }

    /**
     * 插入数据
     *
     * @param string $table 数据表
     * @param array $data 数据数组
     * @return bool
     */
    public function insert(string $table, array $data): bool
    {
        $table = self::$mysqli->real_escape_string($table);
        foreach ($data as $key => $value) {
            $data[$key] = self::$mysqli->real_escape_string($value);
        }
        $keys = '`' . implode('`,`', array_keys($data)) . '`';
        $values = "'" . implode("','", array_values($data)) . "'";
        $sql = "INSERT INTO `{$table}` ( {$keys} ) VALUES ( {$values} )";
        return $this->returnData($sql);
    }

    /**
     * 更新数据
     *
     * @param string $table 数据表
     * @param array $data 数据数组
     * @param array $where 过滤条件
     * @return bool
     */
    public function update(string $table, array $data, array $where): bool
    {
        $table = self::$mysqli->real_escape_string($table);
        $where = $this->jointWhere($where);
        foreach ($data as $key => $value) {
            $data[$key] = self::$mysqli->real_escape_string($value);
        }
        $sets = array();
        foreach ($data as $key => $value) {
            $kstr = '`' . $key . '`';
            $vstr = '\'' . $value . '\'';
            array_push($sets, $kstr . '=' . $vstr);
        }
        $kav = implode(',', $sets);
        $sql = "UPDATE {$table} SET {$kav} WHERE {$where}";
        return $this->returnData($sql);
    }

    /**
     * 删除数据
     *
     * @param string $table 数据表
     * @param array $where 过滤条件
     * @return bool
     */
    public function delete(string $table, array $where): bool
    {
        $where = $this->jointWhere($where);
        $table = self::$mysqli->real_escape_string($table);
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->returnData($sql);
    }
}
