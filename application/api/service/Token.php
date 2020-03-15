<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/10
 * Time: 15:21
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Cache;
use think\facade\Request;

class Token
{
    /**
     * 生成Token
     * @return string
     */
    public static function generateToken()
    {
        //获取32位随机字符串
        $randChars = getRandChar(32);
        //用三组字符串，进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME'];
        //盐
        $salt = config('token.token_salt');
        //md5加密
        return md5($randChars . $timestamp . $salt);

    }

    /**
     * 通过token从缓存中获取对应的值
     * @param $key 键名
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVar( $key )
    {
        //约定token只能通过header传递
        $token = Request::header('token');
        $values = Cache::get($token);
        if (!$values) {
            throw new TokenException();
        } else {
            if (!is_array($values)) {
                $values = json_decode($values , true);
            }
            if (array_key_exists($key , $values)) {
                return $values[$key];
            } else {
                throw new Exception('根据token获取的变量值不存在');
            }
        }
    }

    /**
     * 通过token获取uid
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    /**
     * 前置方法，scope权限 >= 16可以访问
     * 客户端和管理员都可以访问
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    /**
     * 前置方法，scope权限 == 16可以访问
     * 客户端可以访问，管理员不能访问
     * @return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     */
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    /**
     * 检测订单是否属于当前请求用户
     * @param $checkedUID
     * @return bool
     * @throws Exception
     * @throws TokenException
     */
    public static function isValidOperate( $checkedUID )
    {
        if (!$checkedUID) {
            throw new Exception('检测的UID不能为空');
        }
        $tokenUID = self::getCurrentUid();
        if ($checkedUID == $tokenUID) {
            return true;
        }
        return false;
    }

    public static function verifyToken( $token )
    {
        $token = Cache::get($token);
        if(!$token){
            return false;
        }
        return true;
    }
}