<?php
declare(strict_types=1);

namespace icePHP;

class RequireFileException extends \Exception
{
    //目录不存在
    const DIR_NOT_FOUND=1;

    //文件不存在
    const FILE_NOT_FOUND=2;
}