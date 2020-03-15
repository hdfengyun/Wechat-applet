<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/4
 * Time: 10:12
 */

namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\facade\Request;
use think\facade\Log;

class ExceptionHandle extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    public function render( Exception $e )
    {
        if ($e instanceof BaseException) {
            //如果是自定义的异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;

        } else {
            if (config('app.app_debug')) {
                return parent::render($e);
            } else {
                //服务器或系统内部错误
                $this->code = 500;
                $this->msg = '系统内部错误';
                $this->errorCode = 999;//未知错误
                //记录错误日志
                $this->recordErrorLog($e);
            }

        }
        //请求的url地址
        $requestUrl = Request::url();
        $result = [
            'errorCode' => $this->errorCode ,
            'msg' => $this->msg ,
            'request_url' => $requestUrl
        ];

        return json($result , $this->code);

    }


    /**
     * 记录日志
     * @param Exception $e
     */
    private function recordErrorLog( Exception $e )
    {
        //close参数关闭全局日志写入，使用不了record，只能使用write
        Log::write($e->getMessage() , 'error');

    }

}