<?php

namespace app\http\middleware;

use think\facade\Log;
use think\facade\Request;

class CORS
{
    public function handle( $request , \Closure $next )
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
        header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS,PATCH');
        if (Request::isOptions()) {
            return response('' , 200);
        }
        return $next($request);
    }

}
