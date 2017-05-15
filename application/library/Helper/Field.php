<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 10:50
 */
namespace Helper;

class Field{

    /**
     * 验证 多个字段
    */
    static function validates($fields, $data = array())
    {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            foreach ($fields as $k1 => $v1) {
                # 非法字段
                if (false === ($val = self::validate($v1, $k1))) {
                    \Msg::ajax(isset($v1['errormsg'])? $v1['errormsg']: $v1['comment'] . '错误', 0, $k1);
                } # 忽略字段
                elseif (true !== $val) {
                    $data[$k1] = $val;
                }
            }
        } else {
            foreach ($fields as $k1 => $v1) {
                # 非法字段
                if (false === ($val = self::validate($v1, $k1, $data))) {
                    \Msg::ajax(isset($v1['errormsg'])? $v1['errormsg']: $v1['comment'] . '错误', 0, $k1);
                }
            }
        }
        return $data;
    }

    /**
     * 验证 一个字段
    */

    static function validate($field, $key = 0, $data = '')
    {
        # 数据处理(方便操作)
        $key && ($data = isset($data[$key])? $data[$key]: (isset($_POST[$key])? $_POST[$key]: null));
        # 必填
        if (isset($field['required']) && empty($data)) {
            return false;
        }
        # 如果非必填，并且为 null ，则允许通过验证
        if (is_null($data)) {
            return true;
        }
        # 自动格式化数据
        isset($field['format_db']) && $data = Field::format($field['format_db'], $data);
        # 定制验证
        if (isset($field['vv'])) {
            return Validate::$field['vv']($data)? $data: false;
        }
        # 类型 > 正数
        if (strpos($field['type'], 'unsigned') && ($data < 0)) {
            return false;
        }
        # 类型 > 整型
        if (false !== strpos($field['type'], 'int')) {
            return self::int($field['type'], $data);
        }
        # 类型 > 字符串
        if (false !== strpos($field['type'], 'char')) {
            return self::char($field['type'], $data);
        }
        # 类型 > 文本
        if (false !== strpos($field['type'], 'text')) {
            return self::text($field['type'], $data);
        }
        # 类型 > 小数
        if (false !== strpos($field['type'], 'decimal')) {
            return self::decimal($field['type'], $data);
        }
    }

    /**
     * 格式化
    */

    static function format($tpl, $value)
    {
        if (empty($tpl)) return $value;
        return eval('return ' . sprintf($tpl . ';', "'$value'"));
    }

    # 括号处理
    static function round(&$type)
    {
        if ($pos = strpos($type, '(')) {
            return substr($type, $pos + 1, strpos($type, ')') - $pos - 1);
        }
        return '';
    }

    # 整数
    static function int(&$type, &$data)
    {
        if ($data && !is_numeric($data)) return false;
        if ((intval($data)) != $data) return false;
        if (4294967295 < $max = str_pad('', self::round($type), '9')) {
            $max = 4294967295;
        }
        if ($data > $max) return false;
        return intval($data);
    }

    # 小数
    static function decimal(&$type, &$data)
    {
        if ((floatval($data)) != $data) return false;
        list($i, $f) = explode(',', self::round($type));
        if ($data > str_pad('', $i - $f, '9') . '.' . str_pad('', $f, '9')) return false;
        return floatval($data);
    }

    # 字符串
    static function char(&$type, &$data)
    {
        if (mb_strlen($data) > self::round($type)) {
            return false;
        }
        if (!Validate::safe($data)) {
            return false;
        }
        return $data;
    }

    # 文本
    static function text(&$type, &$data)
    {
        if (!Validate::safe($data)) {
            return false;
        }
        return $data;
    }


}