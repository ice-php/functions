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
 * @return bool
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
        return "<{$key}><![CDATA[{$val}]]></{$key}>";
    }
    return "<![CDATA[{$val}]]>";
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

/**
 * 不区分大小写的查找文件并包含(路径区分大小写,文件名不区分)
 * @param $filename string 文件全路径
 * @return bool|mixed
 * @throws RequireFileException
 */
function requireFile(string $filename)
{
    //小写文件名
    $baseLower = strtolower(basename($filename));

    //目录
    $dirName = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, dirname($filename));

    //目录不存在
    if (!is_dir($dirName)) {
        throw new RequireFileException('目录不存在:'.$dirName,RequireFileException::DIR_NOT_FOUND);
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
    throw new RequireFileException('文件不存在:'.$filename,RequireFileException::FILE_NOT_FOUND);
}

/**
 * 判断当前请求是否是Ajax请求
 * @param $forceAjax bool 强制设置为Ajax状态
 * @return bool 当前是否是Ajax状态
 */
function isAjax(bool $forceAjax = false): bool
{
    //记录是否强制指定了Ajax模式
    static $force;

    //如果要求强制,则记录下来
    if ($forceAjax) {
        $force = true;
    }

    //如果已经强制了,返回是
    if ($force) {
        return true;
    }

    //否则 判断是否是Ajax
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * 美化存储容量数字的格式,K,M,G,T
 *
 * @param int $bytes 要转换的数值
 * @param int $precision 精度
 * @return string 转换成KMGT之后的字符串
 */
function kmgt($bytes, $precision = 1): string
{
    $units = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
    $factor = 1;

    foreach ($units as $unit) {
        if ($bytes < $factor * 1024) {
            return number_format($bytes / $factor, $factor > 1 ? $precision : 0) . ' ' . $unit;
        }
        $factor *= 1024;
    }

    $factor /= 1024;
    return number_format($bytes / $factor, $precision) . ' Y';
}

/**
 * 常用的JSON编码,中文不转码
 *
 * @param mixed $something
 * @return string
 */
function json($something): string
{
    return json_encode($something, JSON_UNESCAPED_UNICODE);
}

/**
 * 构造并打印JsonP结果
 * @param $data mixed
 * @throws JsonPException
 */
function jsonP($data): void
{
    header("Content-Type: text/html; charset=utf-8");
    $data = json($data);

    // 如果是JsonP
    if (isset($_REQUEST['callback'])) {
        //检查变量名是否合法
        if (!preg_match('/\w+/i', $_REQUEST['callback'])) {
            throw new JsonPException('当前请求不是JSONP.');
        }
        $callback = $_REQUEST['callback'];
        echo $callback . '(' . $data . ')';
        exit();
    }

    // 普通Ajax
    echo $data;
    exit();
}

/**
 * 时间记录及计算耗时
 *
 * @param $begin int 开始时间
 * @return int 开始时间(如果未指明开始时间)/时间间隔(如果指明时间间隔)
 */
function timeLog($begin = null)
{
    // 不带参数则返回当前时间
    if (!$begin) {
        return microtime(true);
    }

    // 带参数(开始时间),则返回当前时间与开始时间的差
    return round(microtime(true) - $begin, 6);
}

/**
 * 将下划线分隔的名字,转换为驼峰模式
 *
 * @param string $name 下划线分隔的名字
 * @param bool $firstUpper 转换后的首字母是否大写
 * @return string
 */
function formatter(string $name, bool $firstUpper = true): string
{
    // 将表名中的下划线转换为大写字母
    $words = explode('_', $name);
    foreach ($words as $k => $w) {
        $words [$k] = ucfirst($w);
    }

    // 合并
    $name = implode('', $words);

    // 如果明确要求首字母小写
    if (!$firstUpper) {
        $name = lcfirst($name);
    }

    // 返回名字
    return $name;
}

/**
 * 判断是否包含中文
 * @param $str string 要判断的字符串
 * @return bool
 */
function hasCN(string $str): bool
{
    return preg_match('/[\x{4e00}-\x{9fa5}]/ui', $str) !== false;
}

/**
 * 计算下一天(或几天或前几天)的日期
 *
 * @param string $day
 * @param int $n
 * @return string
 */
function nextDay(string $day = '', int $n = 1):string
{
    // 日期的默认值为今天
    if (!$day) {
        $day = date('Y-m-d');
    }

    if ($n > 0)
        return date('Y-m-d', strtotime('+' . $n . ' day', strtotime($day)));

    return date('Y-m-d', strtotime($n . ' day', strtotime($day)));
}