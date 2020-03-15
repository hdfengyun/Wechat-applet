<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/6
 * Time: 11:49
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\api\service\Token as TokenService;
use app\lib\exception\parameterException;

class Token extends BaseController
{
    public function getToken( $code = '' )
    {
        ( new TokenGet() )->goCheck();
        $userobj = new UserToken($code);
        $userToken = $userobj->get();
        return [
            'token' => $userToken
        ];

    }

    public function verifyToken( $token = '' )
    {
        if (!$token) {
            throw new parameterException([
                'msg' => 'token不能为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return [
            'isValid' => $valid ,
        ];

    }

    /**
     * CMS使用API
     * 第三方应用获取令牌
     * @param string $username 用户名
     * @param string $passwood 密码
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getAppToken( $ac = '' , $se = '' )
    {
        ( new AppTokenGet() )->goCheck();
        $app = new AppToken();
        $token = $app->get($ac , $se);
        return [
            'token' => $token
        ];
    }
}