<?php
declare(strict_types=1);

namespace icePHP;

class JsonPException extends \Exception
{
    const DIR_NOT_FOUND=1;
    const FILE_NOT_FOUND=2;
}