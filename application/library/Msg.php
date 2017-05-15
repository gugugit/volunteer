<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 10:57
 */
class Msg{
    /**
     * Json 消息
     *
     * @param string $pMsg 提示信息
     * @param int $pStatus 返回状态
     * @param mixed $pData 要返回的数据
     * @param string $pStatus ajax返回类型
     */
    static function ajax($pMsg = '', $pStatus = 0, $pData = '', $pType = 'json')
    {
        if (defined('JSON_CODE')) {
            exit(strtr(JSON_CODE, array('$msg' => $pMsg)));
        }
        $tResult = array('status' => $pStatus, 'msg' => $pMsg, 'data' => $pData);
        # 格式
        'json' == $pType && exit(json_encode($tResult));
        'xml' == $pType && exit(xml_encode($tResult));
        exit($pData);
    }

    /**
     * 字段错误
     * @param array $field
     * @param string $k1
     */
    static function fielderror($field, $k1)
    {
        Msg::ajax(isset($field['errormsg'])? $field['errormsg']: $field['comment'] . '错误', 0, $k1);
    }

    /**
     * JavaScript 消息
     *
     * @param string $pMsg 消息
     * @param bool $pUrl 网址
     */
    static function js($pMsg = '', $pUrl = false)
    {
        # 页面使用 utf-8
        header('Content-Type: text/html; charset=utf-8');
        # 输出信息
        echo '<script type="text/javascript">';
        # 提示信息
        if ($pMsg) {
            echo "alert('", (is_array($pMsg) ? implode('\n', $pMsg) : $pMsg), "');";
        }
        # 指定跳转
        if ($pUrl) {
            if ('.' == $pUrl) $pUrl = $_SERVER['REDIRECT_URL'];
            echo "self.location='{$pUrl}'";
        } # 后退
        elseif (empty($_SERVER['HTTP_REFERER'])) {
            echo 'window.history.back(-1);';
        } # 返回来源页
        else {
            echo "self.location='{$_SERVER['HTTP_REFERER']}';";
        }
        # 退出
        exit('</script>');
    }

    /**
     * HTML 页面
     */
    static function html($html)
    {
        header('Content-Type: text/html; charset=utf-8');
        exit($html);
    }

}