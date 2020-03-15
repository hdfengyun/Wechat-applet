<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/6
 * Time: 10:48
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $errorCode = 50000;
    public $msg = '指定的分类不存在，请检查参数';
}