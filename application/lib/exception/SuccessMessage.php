<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/11
 * Time: 14:23
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $code = 201;
    public $msg = 'ok';
    public $errorCode = 0;
}