<?php
/**
 * icePHP框架的底层函数库
 * User: 蓝冰大侠
 * Date: 2018/8/31
 * Time: 10:12
 */

declare(strict_types=1);

namespace icePHP;

/**
 * 判断当前是否处于命令行模式
 */
function isCliMode(): bool
{
    return (php_sapi_name() == 'cli');
}

/**
 * 判断当前操作系统是否Windows
 * @return bool
 */
function isWindows(): bool
{
    return strpos(getenv('OS') ?: '', 'Windows') !== false;
}

/**
 * 包含文件,将系统中所有文件引入集中到这里
 * @param $path string 路径
 * @return mixed 文件内容
 */
function requireOnce(string $path)
{
    return require_once($path);
}

//如果未包含MB扩展,则自行定义 mb_strlen方法
if (!function_exists('mb_strlen')) {
    /**
     * 多字节文字的长度
     * @param string $string
     * @return int
     */
    function mb_strlen(string $string = null): int
    {
        // 将字符串分解为单元
        preg_match_all("/./us", $string, $match);
        // 返回单元个数
        return count($match[0]);
    }
}

/**
 * 常用的XML中的CDATA段
 *
 * @param $key string
 * @param $val string
 * @return string
 */
function cdata(string $key, string $val): string
{
    if ($key) {
        return '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
    }
    return '<![CDATA[' . $val . ']]>';
}

/**
 * 常用XML中的CDATA 数组 字段
 * @param array $arr
 * @return string
 */
function cdatas(array $arr): string
{
    $ret = '';
    foreach ($arr as $k => $v) {
        $ret .= cdata($k, $v);
    }
    return $ret;
}

/**
 * 生成 Y-m-d H:i:s的时间字符串
 * 此方法过于常用
 *
 * @param $time int 时间戳
 * @return string 返回 年-月-日 时-分-秒
 */
function datetime(int $time = null): string
{
    return date('Y-m-d H:i:s', $time ?: time());
}

/**
 * 以标准时区显示时间(0时区)
 * @param int $time 时间戳
 * @return string 返回 年-月-日 时-分-秒
 */
function gmdatetime(int $time = null): string
{
    return gmdate('Y-m-d H:i:s', $time ?: time());
}

/**
 * 以Y-m-d格式显示当前日期
 * 常用方法
 *
 * @return string 年-月-日
 */
function today(): string
{
    return date('Y-m-d');
}

/**
 * 取指定定界符中间的内容
 *
 * @param string $content 要截取的字符串
 * @param string|null $beginString 开始定界符
 * @param string|null $endString 结束定界符
 * @return null|string
 */
function mid(string $content, string $beginString = null, string $endString = null): ?string
{
    // 如果提供了开始定界符
    if (!is_null($beginString)) {
        // 计算开始定界符的出现位置
        $beginPos = mb_stripos($content, $beginString);

        // 如果没找到开始定界符,失败
        if ($beginPos === false) {
            return null;
        }

        // 去除开始定界符及以前的内容.
        $content = mb_substr($content, $beginPos + strlen($beginString));
    }

    // 如果未提供结束定界符,直接 返回了.
    if (is_null($endString)) {
        return $content;
    }

    // 计算结束定界符的出现位置
    $endPos = mb_stripos($content, $endString);

    // 如果没找到,失败
    if ($endPos === false) {
        return null;
    }

    // 如果位置为0,返回空字符串
    if ($endPos === 0) {
        return '';
    }

    // 返回 字符串直到定界符开始的地方
    return mb_substr($content, 0, $endPos);
}

/**
 * 简化 字符串 左取
 * @param $str string 母串
 * @param $len int  长度
 * @return string 左取的结果
 */
function left(string $str, int $len = 10): string
{
    return substr($str, 0, intval($len));
}

//此方法可能已经在别的框架中定义
if (!function_exists('dump')) {

    /**
     * 以可读格式显示变量内容
     *
     * @param mixed $vars 变量/数组/...
     * @param string $label 变量名称(可省略)
     * @param boolean $return 返回而不是显示(默认是显示)
     * @return string
     */
    function dump($vars, $label = '', bool $return = false): string
    {
        // 是否输出了UTF8头
        static $outHeader = 0;

        // 第一次要输出UTF8头,以后就不输出了
        if (!$outHeader and !$return and !isCliMode()) {
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' .
                '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            $outHeader = 1;
        }

        $debug = debug_backtrace();
        $from = $debug[0];
        $fromMsg = 'LINE:' . $from['line'] . ' FILE:' . $from['file'];

        // Bool变量,显示为True/False
        if (is_bool($vars)) {
            if ($vars) {
                $vars = 'True';
            } else {
                $vars = 'False';
            }
        }

        if (isCliMode()) {
            $content = $fromMsg . "\r\n" . $label . "\r\n" . print_r($vars, true);
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                $content = iconv('UTF-8', 'GB2312', $content);
            }
        } else {
            // 加上HTML外围标签
            if (ini_get('html_errors') and !$return) {
                $content = "<pre><br/>{$fromMsg}<br/>";
                if ($label != '') {
                    $content .= "<br/><strong>{$label} :</strong><br/>";
                }
                $content .= htmlspecialchars(print_r($vars, true));
                $content .= "<br/></pre><br/>";
            } else {
                $content = "<br/>{$fromMsg}<br/><strong>{$label}</strong> :<br/>" . print_r($vars, true);
            }
        }

        // 不需要返回情况下,打印
        if (!$return) {
            echo $content;
        }

        // 无论如何也要返回
        return $content;
    }
}

/**
 * 不区分大小写的查找文件并包含(路径区分大小写,文件名不区分)
 * @param $filename string 文件全路径
 * @return bool|mixed
 */
function requireFile(string $filename)
{
    //小写文件名
    $baseLower = strtolower(basename($filename));

    //目录
    $dirName = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname($filename));

    //目录不存在
    if (!is_dir($dirName)) {
        return false;
    }

    //此目录下的所有 文件 及文件 夹
    $dir = dir($dirName);

    //遍历 查看
    while ($file = $dir->read()) {
        //文件夹略过
        if ($file == '.' or $file == '..' or is_dir($dirName . '/' . $file)) {
            continue;
        }

        //不区分大小写并匹配
        if (strtolower($file) == $baseLower) {
            return require($dirName . '/' . $file);
        }
    }

    //未找到
    return false;
}
