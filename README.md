一些常用的基本函数
==========================================================
 这里定义的函数可以被icePHP框架所使用，也可以被单独引用。本函数库不依赖其它Git代码库。


* 运行模式判断

    isCliMode():bool
    
    判断当前是否处于命令行模式。

* 操作系统判断

    isWindows(): bool
    
    判断当前操作系统是否Windows。

* 包含文件

    requireOnce(string $path)
    
    将系统中所有文件引入集中到这里。

* 常用的XML中的CDATA段
    
    cdata(string $key, string $val): string
    
    如果key不为空，返回：
    ~~~xml
    <key><![CDATA[val]]></key>
    ~~~
      
    如果key为空，返回：
    ~~~xml
    <![CDATA[val]]>
    ~~~
  
* 常用XML中的CDATA 数组 字段

    cdatas(array $arr): string
    
    对数组中的每一个键值对，按CDATA方法生成，并拼接返回。

* 生成时间字符串

    datetime(int $time = null): string
    
    以Y-m-d H:i:s格式生成日期时间字符串。

* 生成0时区时间字符串

    gmdatetime(int $time = null): string
    
    以Y-m-d H:i:s格式生成标准日期时间(0时区)字符串。

* 生成当前日期字符串
    
    today(): string
    
    生成Y-m-d格式的当前日期字符串。

* 取指定定界符中间的内容

    mid(string $content, string $beginString = null, string $endString = null): ?string

* 打印变量或表达式的值
    
    dump($vars, $label = '', bool $return = false): string
    
    类似var_dump，以可读格式显示变量内容。

* 文件包含
    
    requireFile(string $filename)
    
    不区分大小写的查找文件并包含(路径区分大小写,文件名不区分)。

* Ajax判断
    
    isAjax(bool $forceAjax = false): bool
    
    判断当前请求是否是Ajax请求；
    
    如果传入参数为True，则下次请求判断时将返回True，表明本次请求强制为Ajax。

* 美化存储容量数字的格式,K,M,G,T

    kmgt($bytes, $precision = 1):string
    
    生成结果示例：4.9 M

* JSON编码

    json($something): string
    
    常用的JSON编码，中文不转码。

* 输出JsonP结果
    
    jsonP($data): void
    
    构造并打印JsonP结果。

* 时间记录及计算耗时

    timeLog($begin = null)
    
    不传入参数，将返回当前时间戳；
    
    传入一个时间戳，将返回当前时间与指定时间的间隔；
    
    用来计算一段操作的耗时。

* 名字转换

    formatter(string $name, bool $firstUpper = true): string
    
     将下划线分隔的名字,转换为驼峰模式。
         
* 中文判断

    hasCN(string $str): bool
    
    判断指定字符串是否包含中文。   
    