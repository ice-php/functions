# functions
这是ice-php框架的底层函数库, 用来定义一些常用的全局函数.

# isCliMode():bool
判断当前是否处于命令行模式

# isWindows(): bool
判断当前操作系统是否Windows

# requireOnce(string $path)
包含文件,将系统中所有文件引入集中到这里

# mb_strlen(string $string = null): int
如果未包含MB扩展,则自行定义 mb_strlen方法

# cdata(string $key, string $val): string
常用的XML中的CDATA段

# cdatas(array $arr): string
常用XML中的CDATA 数组 字段

# datetime(int $time = null): string
生成 Y-m-d H:i:s的时间字符串

# gmdatetime(int $time = null): string
以标准时区显示时间(0时区)

# today(): string
以Y-m-d格式显示当前日期

# mid(string $content, string $beginString = null, string $endString = null): ?string
取指定定界符中间的内容

# dump($vars, $label = '', bool $return = false): string
以可读格式显示变量内容

# requireFile(string $filename)
不区分大小写的查找文件并包含(路径区分大小写,文件名不区分)


