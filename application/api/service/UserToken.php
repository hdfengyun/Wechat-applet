<?php
/**
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/6/6
 * Time: 12:00
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    public function __construct( $code )
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url') , $this->wxAppID , $this->wxAppSecret , $this->code);
    }

    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result , true);
        if (empty($wxResult)) {
            throw new Exception('获取session_key及openid时异常，微信内部异常');
        } else {
            $loginFail = array_key_exists('errcode' , $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            } else {
                //微信接口调用成功
                return $this->grantToken($wxResult);
            }
        }
    }

    /**
     * 检查生成Token
     * @param $wxResult
     * @return string
     * @throws TokenException
     */
    private function grantToken( $wxResult )
    {
        //1.拿到openid
        //2.去数据库查询，是否存在这个openid记录的用户
        //3.如果存在不处理，不存在新增加一条user记录
        //4.生成令牌，准备缓存数据，存入缓存
        //5.将token令牌返回到客户端去
        //key:token令牌
        //value：$wxResult, $uid ,$scope
        $openid = $wxResult['openid'];
        $userResult = UserModel::getByOpenID($openid);
        if ($userResult) {
            $uid = $userResult->id;
        } else {
            //不存在，新增加一条记录
            $uid = $this->addUser($wxResult);
        }
        //准备缓存的value值
        $cacheValue = $this->prepareCachedValue($wxResult , $uid);
        //生成Token
        $token = $this->saveToCache($cacheValue);

        return $token;

    }

    private function saveToCache( $cacheValue )
    {
        $key = self::generateToken();
        $value = json_encode($cacheValue);
        $expire_in = config('token.token_expire_in');
        $request = cache($key , $value , $expire_in);
        if (!$request) {
            throw new TokenException([
                'errorCode' => 10005 ,
                'msg' => '服务器缓存异常'
            ]);
        }
        return $key;
    }

    /**
     * 准备缓存value
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function prepareCachedValue( $wxResult , $uid )
    {
        $cacheValue = $wxResult;
        $cacheValue['uid'] = $uid;
        $cacheValue['scope'] = ScopeEnum::User;//权限
        return $cacheValue;

    }

    /**
     * 新增一条user
     * @param $wxResult
     * @return mixed
     */
    private function addUser( $wxResult )
    {
        $user = UserModel::create([
            'openid' => $wxResult['openid'] ,
            'session_key' => $wxResult['session_key']
        ]);
        return $user->id;
    }

    /**
     * 处理微信内部错误
     * @param $wxResult
     * @throws WeChatException
     */
    private function processLoginError( $wxResult )
    {
        throw new WeChatException([
            'msg' => $wxResult['errmsg'] ,
            'errorCode' => $wxResult['errcode']
        ]);

    }
}