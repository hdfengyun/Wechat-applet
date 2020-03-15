<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/4
 * Time: 15:01
 */

namespace app\lib\exception;


class parameterException extends BaseException
{
    public $code = 400;
    public $errorCode = 10000;
    public $msg = '参数错误';
}