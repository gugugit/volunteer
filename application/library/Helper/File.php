<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/11
 * Time: 22:56
 */
namespace Helper;

class File{

    # 记日志
    function writelog($file, $msg, $code = 'notice'){
        static $cnt = 0, $handle;
        # 超过 10 万行，创建新日志文件
        if($cnt > 99999){
            $cnt = 0;
            fclose($handle);
        }
        # 初始化文件句柄
        $cnt || $handle = fopen(APP_PATH.'/log/'.$file, 'a');
        # 写日志
        fwrite($handle, date('[Y-m-d H:i:s]')." [$code] $msg\n");
        # 行数统计
        ++$cnt;
    }
    /**
     * 上传图片
     * @param string $path 存储的目录（绝对路径）
     * @param string $fn input的name
     * @param string $filename 文件名
     * @param int $max 最大上传个数 (一个秀最多20张图片)
     * @param int $maxSize 长、宽  最大值
     * @param null $imgSizes 用于记录各个图片大小的数组
     * @return int
     */
    static function upimgs($path, $fn = 'photo', $filename = '',$type ='gif',$max = 9, $maxSize = 600, &$imgSizes = null)
    {
        $imgType = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
        # 参数错误
        if (empty($_FILES[$fn])) return 0;
        $isupload = 0;
        file_exists($path) || @mkdir($path, 0777, true);
        # 循环上传
        for ($i = 0; $i < $max; ++$i) {
            if (!isset($_FILES[$fn]['tmp_name'][$i])) continue;
            # 上传错误
            if ($_FILES[$fn]['error'][$i]) continue;
            # 非图片
            if (!$size = getimagesize($_FILES[$fn]['tmp_name'][$i])) continue;
            # 图片类型不正确
            if (empty($imgType[$size[2]])) continue;
            # maxSize为0 不压缩
            if ($maxSize > 0) {
                if ($size[0] / $size[1] > 1) {
                    $width = $maxSize;
                    $height = intval($width * $size[1] / $size[0]);
                } else {
                    $height = $maxSize;
                    $width = intval($height * $size[0] / $size[1]);
                }
            } else {
                $width = $size[0];
                $height = $size[1];
            }
            # 记录图片宽高
            if (is_array($imgSizes)) {
                $imgSizes[$i] = array($width, $height);
            }
            $fnc = 'imagecreatefrom' . $imgType[$size[2]];
            $img = $fnc($_FILES[$fn]['tmp_name'][$i]);
            # $img2 = imagecreatetruecolor($width, $height); 失真严重,使用原图
            # imagecopyresized($img, $img, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
            $dstFile = $path . ((is_array($filename) && isset($filename[$i])) ? $filename[$i] : ($filename . $i)) . '.'.$type;
            file_exists($dstFile) && unlink($dstFile);
            imagejpeg($img, $dstFile, 80);
            if ($max == ++$isupload) break;
        }
        return $isupload;
    }

    /**
     * 删除文件夹及其文件夹下所有文件
     * @param string $dir 文件路径
     * @return bool
     */
    static function deldir($dir)
    {
        # 文件夹不存在 直接返回
        if(!file_exists($dir)) return;
        # 先删除目录下的文件
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::deldir($fullpath);
                }
            }
        }
        closedir($dh);
        # 删除当前文件夹：
        return rmdir($dir);
    }
}