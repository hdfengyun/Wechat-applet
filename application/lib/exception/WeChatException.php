<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/10
 * Time: 14:24
 */

namespace app\lib\exception;


class WeChatException extends BaseException
{
    public $code = 400;
    public $msg = '微信服务器接口调用失败';
    public $errorCode = 999;
}