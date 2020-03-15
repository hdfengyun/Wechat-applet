<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/11
 * Time: 16:19
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $errorCode = 10001;
    public $msg = '无访问权限';

}