<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/4
 * Time: 10:16
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code = 404;
    public $errorCode = 40000;
    public $msg = '请求的banner不存在';
}