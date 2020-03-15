<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/12
 * Time: 14:41
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $errorCode = 80000;
    public $msg = '订单不存在，请检查参数';
}