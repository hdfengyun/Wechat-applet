<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/11
 * Time: 11:59
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $msg = '用户不存在';
    public $errorCode = 60000;
}