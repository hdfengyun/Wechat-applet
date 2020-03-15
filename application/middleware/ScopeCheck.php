<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/11
 * Time: 16:10
 */

namespace app\middleware;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use think\Middleware;
use think\Request;
use app\api\service\Token as TokenService;

class ScopeCheck extends Middleware
{

    public function handle( Request $request , \Closure $next )
    {
        if (TokenService::getCurrentTokenVar('scope') < ScopeEnum::User) {
            throw new ForbiddenException();
        }

        return $next($request);
    }
}