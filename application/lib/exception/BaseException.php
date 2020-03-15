<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/4
 * Time: 10:13
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    //http状态码
    public $code = 400;
    //错误消息
    public $msg = '参数错误';
    //错误码
    public $errorCode = 10000;

    public function __construct( $param = [] )
    {
        if(!is_array($param)){
            return;
        }
        if(array_key_exists('code',$param)){
            $this->code = $param['code'];
        }
        if(array_key_exists('errorCode',$param)){
            $this->errorCode = $param['errorCode'];
        }
        if(array_key_exists('msg',$param)){
            $this->msg = $param['msg'];
        }
    }
}