<?php
declare(strict_types=1);

namespace icePHP;

class RequireFileException extends \Exception
{
    const DIR_NOT_FOUND=1;
    const FILE_NOT_FOUND=2;
}