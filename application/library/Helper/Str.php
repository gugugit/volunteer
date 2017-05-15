<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 18:02
 */
namespace Helper;

class Str
{
    /**
     * 替换危险字符串
     * @param string $str 危险字符
     * @param array $rm 自定义替换规则
     *
     * @return string 安全字符
     */
    static function safe($str, $rm = array())
    {
        $trans = array("'" => '', '"' => '', '`' => '', '\\' => '', '<' => '＜', '>' => '＞');
        $rm && $trans = array_merge($rm, $rm);
        return strtr(trim($str), $trans);
    }

    /**
     * 迭代过滤
     */
    static function filter(&$data)
    {
        if (is_array($data)) {
            foreach ($data as &$v1) self::filter($v1);
        } else {
            $data = trim($data);
        }
    }

    /**
     * 获得 KEY 对应的 数组值
     */
    static function arr2str($pArr, $pKey, $pDefault = '')
    {
        return isset($pArr[$pKey])? $pArr[$pKey]: $pDefault;
    }

    /**
     * 整数转枚举(二进制)
     */
    static function int2enum($data, $int, $return = 'string')
    {
        $enum = array();
        foreach ($data as $k1 => $v1) if ($k1 & $int) $enum[$k1] = $v1;
        if ('array' == $return) return $enum;
        return join(' ', $enum);
    }

    /**
     * 数字转为字符
     * @param int $n 10进制
     *
     * @return string 62进制
     */
    static function int2str($n)
    {
        $ret = '';
        for ($t = floor(log10($n) / log10(62)); $t >= 0; $t--) {
            $a = floor($n / pow(62, $t));
            $ret .= substr('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $a, 1);
            $n -= $a * pow(62, $t);
        }
        return $ret? $ret: 0;
    }

    /**
     * 字符转为数字
     * @param string $n 62进制
     *
     * @return int 10进制
     */
    static function str2int($s)
    {
        $ret = 0;
        $len = strlen($s) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $ret += strpos('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', substr($s, $t, 1)) * pow(62, $len - $t);
        }
        return $ret;
    }

    /**
     * ID 转为 文件路径
     */
    static function id2path($id, $str = false)
    {
        $id = str_pad(self::int2str($id), 6, 0, 0);
        if (false === $str) {
            return array(substr($id, 0, 2) . '/' . substr($id, 2, 2) . '/', substr($id, 4));
        }
        return substr($id, 0, 2) . '/' . substr($id, 2, 2) . '/' . substr($id, 4) . $str;
    }

    /**
     * 加密
     * @param string $data 密文
     * @param string $key 密钥
     *
     * @return string
     */
    static function encrypt($data, $key)
    {
        $char = $str = '';
        for ($i = $x = 0, $l = strlen($key = md5($key)), $len = strlen($data); $i < $len; $i++, ++$x) {
            $x == $l && $x = 0;
            $char .= $key{$x};
        }
        for ($i = 0; $i < $len; $i++) $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        return base64_encode($str);
    }

    /**
     * 解密
     * @param string $data 密文
     * @param string $key 密钥
     *
     * @return string
     */
    static function decrypt($data, $key)
    {
        $char = $str = '';
        for ($i = $x = 0, $l = strlen($key = md5($key)), $len = strlen($data = base64_decode($data)); $i < $len; $i++, ++$x) {
            $x == $l && $x = 0;
            $char .= substr($key, $x, 1);
        }
        for ($i = 0; $i < $len; $i++) {
            $o3 = ($o1 = ord(substr($data, $i, 1))) < ($o2 = ord(substr($char, $i, 1)))? 256: 0;
            $str .= chr($o1 + $o3 - $o2);
        }
        return $str;
    }

    /**
     * 打码 > 人名
     */
    static function formatName($name)
    {
        if (empty($name)) return '';
        return '*' . mb_substr($name, -(mb_strlen($name, 'utf-8') - 1), mb_strlen($name, 'utf-8'), 'utf-8');
    }

    /**
     * 打码 > 身份证
     */
    static function formatIdCard($idcard)
    {
        if (empty($idcard)) return '';
        return str_repeat("*", strlen($idcard) - 4) . substr($idcard, -4, 4);
    }

    /**
     * 打码 > 手机号
     */
    static function formatMobile($mobile)
    {
        if (empty($mobile)) return '';
        return substr($mobile, 0, 3) . '****' . substr($mobile, -4, 4);
    }

    /**
     * 打码 > 邮箱
     */
    static function formatEmail($email)
    {
        if (empty($email)) return '';
        return substr($email, 0, 1) . '****' . substr($email, strrpos($email, '@'));
    }

    /**
     * 打码 > 银行卡号
     */
    static function formatAcnumber($acnumber)
    {
        if (empty($acnumber)) return '';
        return substr($acnumber, 0, 4) . '*****' . substr($acnumber, -4, 4);
    }

    /**
     * 查询地区
     */
    /**
     * @param array $data 数据记录，格式如：array('province'=>1, 'city'=>10, 'district'=>100)
     * @param \Redis $redis REDIS
     *
     * @return string
     */
    static function address($data, &$redis, $keys = 'province,city,district')
    {
        $address = array();
        foreach (explode(',', $keys) as $v1)
            if ($data[$v1] && ($v1 = json_decode($redis->hget('ADDRESS', $data[$v1]), true)))
                $address[] = $v1['name'];
        return $address? join(' ', $address): '未知';
    }

    /**
     * 联表查（仅用于数据量较小的情况）
     * @param string $config 配置
     *
     * @return mixed
     */
    static function join($config)
    {
        static $data = array();
        # 已读取过数据
        $where = isset($config['JOIN_WHERE'])? $config['JOIN_WHERE']: '';
        if (isset($data[$config['JOIN'] . $where])) {
            return $data[$config['JOIN'] . $where];
        }
        # 获取数据
        list($table, $fk) = explode('.', $config['JOIN']);
        $table = (strpos($table, '_')? str_replace(' ', '_', ucwords(str_replace('_', ' ', $table))): ucwords($table)) . 'Model';
        $model = new $table();
        return $data[$config['JOIN']] = $model->where($where)->fHash("$fk,name");
    }

    /**
     * 格式化字段
     * @param $tpl
     * @param $value
     *
     * @return mixed
     */
    static function formatField($tpl, $value)
    {
        if (empty($tpl)) return $value;
        return eval('return ' . sprintf($tpl . ';', "'$value'"));
    }

    /**
     * 字段 隐藏
     * @param $field
     * @param $page
     *
     * @return bool
     */
    static function hide($field, $page)
    {
        return isset($field['is_hide']) && $field['is_hide'] & $page;
    }

    /**
     * 二维数组转为一维数组
     * @param $arr 二维数组
     *
     * @return array 一维数组
     */
    static function rebuildarray($arr)
    {
        static $tmp = array();
        foreach ($arr as $item) {
            foreach ($item as $items) {
                $tmp[] = $items;
            }
        }
        return $tmp;
    }

    /**
     * 字符串防止SQL注入
     * @param $sql_str
     *
     * @return mixed
     */
    static function sql_replace($sql_str)
    {
        $regex = "/\/|\~|select|insert|update|delete| |\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\||/";
        return preg_replace($regex,"",$sql_str);
    }
}
