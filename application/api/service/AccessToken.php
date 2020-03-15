<?php
/**
 * 微信AccessToken管理类
 * Created by PhpStorm.
 * User: sdw
 * Date: 2019/7/5
 * Time: 14:56
 */

namespace app\api\service;


use think\Exception;

class AccessToken
{
    private $tokenUrl;//获取AccessToken的微信请求地址
    const TOKEN_CACHED_KEY = 'access';//缓存键名key
    const TOKEN_EXPIRE_IN = 7000;//过期时间

    function __construct()
    {
        $url = config('wx.access_token_url');
        $url = sprintf($url , config('wx.app_id') , config('wx.app_secret'));
        $this->tokenUrl = $url;
    }

    // 建议用户规模小时每次直接去微信服务器取最新的token
    // 但微信access_token接口获取是有限制的 2000次/天
    public function get()
    {
        $token = $this->getFromCache();
        if (!$token) {
            return $this->getFromWxServer();
        } else {
            return $token;
        }
    }

    /**
     * 从缓存中读取AccessToken
     * @return mixed|null
     */
    private function getFromCache()
    {
        $token = cache(self::TOKEN_CACHED_KEY);
        if (!$token) {
            return $token;
        }
        return null;
    }

    /**
     * 缓存中AccessToken不存在或失效了，重新向微信获取
     * @return mixed
     * @throws Exception
     */
    private function getFromWxServer()
    {
        $token = curl_get($this->tokenUrl);
        $token = json_decode($token , true);
        if (!$token) {
            throw new Exception('获取AccessToken异常');
        }
        if (!empty($token['errcode'])) {
            throw new Exception($token['errmsg']);
        }
        $this->saveToCache($token);
        return $token['access_token'];
    }

    /**
     * 将AccessToken存入缓存中
     * @param $token
     */
    private function saveToCache( $token )
    {
        cache(self::TOKEN_CACHED_KEY , $token , self::TOKEN_EXPIRE_IN);
    }
}