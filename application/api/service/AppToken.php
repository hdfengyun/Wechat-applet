<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/7/5
 * Time: 10:22
 */

namespace app\api\service;


use app\api\model\ThirdApp;
use app\lib\exception\TokenException;

class AppToken extends Token
{

    public function get( $ac , $se )
    {
        $app = ThirdApp::check($ac , $se);
        if (!$app) {
            throw new TokenException([
                'errorCode' => 10004 ,
                'msg' => '授权失败'
            ]);
        } else {
            $scope = $app->scope;
            $uid = $app->id;
            $values = [
                'scope' => $scope ,
                'uid' => $uid
            ];
            $token = $this->saveToCache($values);
            return $token;
        }
    }

    private function saveToCache( $values )
    {
        $token = self::generateToken();
        $expire_in = config('token.token_expire_in');
        $result = cache($token , json_encode($values) , $expire_in);
        if (!$result) {
            throw new TokenException([
                'errorCode' => 10005 ,
                'msg' => '服务器缓存异常'
            ]);
        }
        return $token;
    }

}